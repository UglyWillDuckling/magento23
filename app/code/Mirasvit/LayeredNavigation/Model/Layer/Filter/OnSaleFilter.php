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

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Mirasvit\LayeredNavigation\Api\Service\FilterDataServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterOnSaleServiceInterface;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;

/**
 * OnSale filter
 */
class OnSaleFilter extends AbstractFilter
{
    use ConfigTrait;

    /**
     * @var string
     */
    protected $attributeCode = AdditionalFiltersConfigInterface::ON_SALE_FILTER;

    /**
     * @var bool
     */
    protected $isAdded = false;

    /**
     * @var bool
     */
    protected static $isStateAdded = [];

    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        FilterDataServiceInterface $filterDataService,
        FilterOnSaleServiceInterface $filterOnSaleService,
        AdditionalFiltersConfigInterface $additionalFiltersConfig,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->_requestVar = AdditionalFiltersConfigInterface::ON_SALE_FILTER_FRONT_PARAM;
        $this->filterDataService = $filterDataService;
        $this->filterOnSaleService = $filterOnSaleService;
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->additionalFiltersConfig->isOnSaleFilterEnabled($this->storeManager->getStore()->getStoreId())) {
            return $this;
        }
        $filter = $request->getParam(AdditionalFiltersConfigInterface::ON_SALE_FILTER_FRONT_PARAM);

        if ($filter) {
            //work with collection in plugin
            $this->addState($this->getName(), $filter);
            $this->isAdded = true;
        }

        return $this;
    }

    /**
     * @param string $label
     * @param string $filter
     * return void
     */
    private function addState($label, $filter)
    {
        $state = $this->_requestVar . $filter;
        if (isset(self::$isStateAdded[$state])) { //avoid double state adding (horizontal filters)
            return true;
        }
        $this->getLayer()->getState()->addFilter($this->_createItem($label, $filter));

        self::$isStateAdded[$state] = true;

        return true;
    }

    /**
     * Get filter text label
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getName()
    {
        $saleName = $this->additionalFiltersConfig->getOnSaleFilterLabel($this->storeManager->getStore()->getStoreId());
        $saleName = ($saleName) ? : AdditionalFiltersConfigInterface::ON_SALE_FILTER_DEFAULT_LABEL;

        return $saleName;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if (!$this->additionalFiltersConfig->isOnSaleFilterEnabled($this->storeManager->getStore()->getStoreId())
            || $this->isAdded && !ConfigTrait::isMultiselectEnabled()) {
                return [];
        }

        $productCollection = $this->getLayer()->getProductCollection();
        $requestBuilder = clone $productCollection->getCloneRequestBuilder();
        $requestBuilder->removePlaceholder($this->attributeCode);
        $queryRequest = $requestBuilder->create();
        $optionsFacetedData = $this->filterDataService->getFilterBucketData($queryRequest, $this->attributeCode);
        $count = isset($optionsFacetedData[1]) ? $optionsFacetedData[1]['count'] : 0;

        $optionsData = [
            [
                'label' => $this->getName(),
                'value' => 1,
                'count' => $count,
            ]
        ];

        foreach ($optionsData as $data) {
            if($data['count'] < 1) {
                continue;
            }
            $this->itemDataBuilder->addItemData(
                $data['label'],
                $data['value'],
                $data['count']
            );
        }

        return $this->itemDataBuilder->build();
    }
}
