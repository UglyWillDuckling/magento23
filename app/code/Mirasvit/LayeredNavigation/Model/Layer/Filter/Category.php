<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.0.59
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Model\Layer\Filter;

use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Framework\Escaper;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\App\RequestInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterDataServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Category filter
 */
class Category extends AbstractFilter
{
    use ConfigTrait;

    const ATTRIBUTE = 'category_ids';
    const STORE = 'store_id';
    const CATEGORY = 'category';
    const BRAND_PAGE = 'brand_brand_view';
    const ALL_PRODUCTS_PAGE = 'all_products_page_index_index';
    const CATEGORY_SECOND_WAY_ACTIONS = [self::BRAND_PAGE, self::ALL_PRODUCTS_PAGE];

    /**
     * @var bool
     */
    protected static $isStateAdded = [];

    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        Escaper $escaper,
        CategoryFactory $categoryDataProviderFactory,
        CategoryRepositoryInterface $categoryRepository,
        LayerResolver $layerResolver,
        RequestInterface $request,
        FilterDataServiceInterface $filterDataService,
        FilterClearBlockConfigInterface $filterClearBlockConfig,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->escaper = $escaper;
        $this->_requestVar = 'cat';
        $this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->categoryRepository = $categoryRepository;
        $this->layer = $layerResolver->get();
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->filterDataService = $filterDataService;
        $this->filterClearBlockConfig = $filterClearBlockConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->objectManager = $objectManager;
    }


    /**
     * Apply category filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!ConfigTrait::isMultiselectEnabled()) {
            return $this->getDefaultApply($request);

        }
        $categoryId = $this->request->getParam($this->getRequestVar()) ?: $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }
        $categoryIds = explode(',', $categoryId);
        $categoryIds = array_unique($categoryIds);
        $categoryIds = array_map('intval', $categoryIds); //must be int
        $categoryIds = array_diff($categoryIds, ['', 0, false, null]); //don't use incorrect data
        $productCollection = $this->getLayer()->getProductCollection();

        if ($request->getParam('id') != $categoryId) {
            $productCollection->addCategoryMultiFilter($categoryIds);

            $category = $this->getLayer()->getCurrentCategory();

            $child = $category->getCollection()
                ->addFieldToFilter($category->getIdFieldName(), $categoryIds)
                ->addAttributeToSelect('name');
            $this->addState(false, $categoryIds, $child);
        }

        return $this;
    }

    /**
     * Add data to state
     *
     * @return bool
     */
    protected function addState($categoryName, $categoryId, $child = false)
    {
        $state = is_array($categoryId)
            ? $this->_requestVar . implode('_', $categoryId) : $this->_requestVar . $categoryId;
        if (isset(self::$isStateAdded[$state])) { //avoid double state adding (horizontal filters)
            return true;
        }

        if (is_array($categoryId) && $child && $this->filterClearBlockConfig->isFilterClearBlockInOneRow()) {
            $labels = [];
            foreach ($categoryId as $categoryIdValue) {
                if ($currentCategory = $child->getItemById($categoryIdValue)) {
                    $labels[] = $currentCategory->getName();
                }
            }
            $this->getLayer()->getState()->addFilter(
                $this->_createItem(implode(', ', $labels),
                    $categoryId
                )
            );
        } elseif (is_array($categoryId) && $child) {
            foreach ($categoryId as $categoryIdValue) {
                if ($currentCategory = $child->getItemById($categoryIdValue)) {
                    $this->getLayer()->getState()->addFilter(
                        $this->_createItem($currentCategory->getName(),
                            $categoryIdValue
                        )
                    );
                }
            }
        } else {
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($categoryName,
                    $categoryId
                )
            );
        }

        self::$isStateAdded[$state] = true;

        return true;
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return __('Category');
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $optionsFacetedData = $this->getFacetedData();
        $category = $this->dataProvider->getCategory();

        if ($category->getIsActive()
            && in_array($this->request->getFullActionName(), self::CATEGORY_SECOND_WAY_ACTIONS)) {
                $categoryData = $this->getPreparedCategoryData($optionsFacetedData);
                foreach ($categoryData as $data) {
                    foreach ($data as $dataByLevel) {
                        $this->itemDataBuilder->addItemData(
                            $dataByLevel['category_name'],
                            $dataByLevel['category_id'],
                            $dataByLevel['count']
                        );
                    }
                }
        } elseif ($category->getIsActive()) {
            $categories = $category->getChildrenCategories();
            foreach ($categories as $category) {
                if ($category->getIsActive()
                    && isset($optionsFacetedData[$category->getId()])
                ) {
                    $this->itemDataBuilder->addItemData(
                        $this->escaper->escapeHtml($category->getName()),
                        $category->getId(),
                        $optionsFacetedData[$category->getId()]['count']
                    );
                }
            }
        }

        $itemsData =  $this->itemDataBuilder->build();

        if (count($itemsData) == 1) {
            $collectionSize = $this->getLayer()->getProductCollection()->getSize();
            if (!$this->isOptionReducesResults($itemsData[0]['count'], $collectionSize)) {
                $itemsData = [];
            }
        }

        return $itemsData;
    }

    /**
     * Get prepared category data to build category filters
     * for following actions 'brand_brand_view', 'all_products_page_index_index'
     *
     * @return array
     */
    protected function getPreparedCategoryData($optionsFacetedData)
    {
        if (!$optionsFacetedData) {
            return [];
        }

        $categoryData = [];
        foreach ($optionsFacetedData as $categoryId => $optionsFaceted) {
            $category = $this->categoryRepository->get($categoryId, $this->storeId);
            if (is_object($category) && $category->getIsActive()
                && isset($optionsFacetedData[$category->getId()])
            ) {
                $categoryData[$category->getLevel()][$categoryId] = [
                    'category_name' => $this->escaper->escapeHtml($category->getName()),
                    'category_id' => $categoryId,
                    'count' => $optionsFacetedData[$categoryId]['count'],
                ];
            }
        }

        $fullActionName = $this->request->getFullActionName();
        if (($fullActionName == self::BRAND_PAGE
            && interface_exists(\Mirasvit\Brand\Api\Config\GeneralConfigInterface::class)
            && $this->objectManager->get(\Mirasvit\Brand\Api\Config\GeneralConfigInterface::class)
                ->isShowAllCategories())
            || ($fullActionName == self::ALL_PRODUCTS_PAGE
                && interface_exists(\Mirasvit\AllProducts\Api\Config\ConfigInterface::class)
                && $this->objectManager->get(\Mirasvit\AllProducts\Api\Config\ConfigInterface::class)
                    ->isShowAllCategories())) {
            $categoryDataPrepared = $categoryData;
        } else {
            $toLevel = min(array_keys($categoryData));
            $categoryDataPrepared[$toLevel] = $categoryData[$toLevel];
        }

        return $categoryDataPrepared;
    }

    /**
     * @return array
     */
    protected function getFacetedData()
    {
        $productCollection = $this->getLayer()->getProductCollection();
        $startCategoryForCountBucket = $this->layer->getCurrentCategory();
        $requestBuilder = clone $productCollection->getCloneRequestBuilder();
        $requestBuilder->removePlaceholder(self::ATTRIBUTE);
        $requestBuilder->removePlaceholder(self::STORE);
        $requestBuilder->bind(self::STORE, $this->getStoreId());
        $requestBuilder->bind(self::ATTRIBUTE, $startCategoryForCountBucket->getId());
        $queryRequest = $requestBuilder->create();
        $optionsFacetedData = $this->filterDataService->getFilterBucketData($queryRequest, self::CATEGORY);

        return $optionsFacetedData;
    }

    /**
     * Apply category filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    protected function getDefaultApply($request)
    {
        if ($request->getRouteName() == ConfigInterface::IS_CATALOG_SEARCH) {
            return $this->getCatalogSearchApply($request);
        } else {
            return $this->getCatalogApply($request);
        }
    }

    /**
     * Apply category filter to layer
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    private function getCatalogApply(\Magento\Framework\App\RequestInterface $request)
    {
        $categoryId = (int)$request->getParam($this->getRequestVar());
        if (!$categoryId) {
            return $this;
        }

        $this->dataProvider->setCategoryId($categoryId);

        if ($this->dataProvider->isValid()) {
            $category = $this->dataProvider->getCategory();
            $this->getLayer()->getProductCollection()->addCategoryFilter($category);
            $this->addState($category->getName(), $categoryId);
        }

        return $this;
    }

    /**
     * Apply category filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    private function getCatalogSearchApply(\Magento\Framework\App\RequestInterface $request)
    {
        $categoryId = $request->getParam($this->_requestVar) ?: $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }

        $this->dataProvider->setCategoryId($categoryId);

        $category = $this->dataProvider->getCategory();

        $this->getLayer()->getProductCollection()->addCategoryFilter($category);

        if ($request->getParam('id') != $category->getId() && $this->dataProvider->isValid()) {
            $this->addState($category->getName(), $categoryId);
        }
        return $this;
    }
}
