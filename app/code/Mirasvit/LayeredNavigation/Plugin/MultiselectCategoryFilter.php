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


namespace Mirasvit\LayeredNavigation\Plugin;

use Magento\Framework\Search\RequestInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\Request\Query\BoolExpression;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\CatalogSearch\Model\Adapter\Aggregation\Checker\Query\CatalogView;

class MultiselectCategoryFilter
{
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    public function aroundIsApplicable(
        CatalogView $subject,
        \Closure $proceed,
        RequestInterface $request
    )
    {
        if ($request->getName() === 'catalog_view_container') {
            return $this->hasAnchorCategory($request);
        }

        return $proceed($request);
    }

    /**
     * Check whether category is anchor.
     *
     * Proceeds with request and check whether at least one of categories is anchor.
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function hasAnchorCategory(RequestInterface $request)
    {
        $queryType = $request->getQuery()->getType();
        $result = false;

        if ($queryType === QueryInterface::TYPE_BOOL) {
            $categories = $this->getCategoriesFromQuery($request->getQuery());

            /** @var \Magento\Catalog\Api\Data\CategoryInterface $category */
            foreach ($categories as $category) {
                // It's no need to render LN filters for non anchor categories
                if ($category && $category->getIsAnchor()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get categories based on query filter data.
     *
     * Get categories from query will allow to check if category is anchor
     * And proceed with attribute aggregation if it's not
     *
     * @param QueryInterface $queryExpression
     * @return \Magento\Catalog\Api\Data\CategoryInterface[]|[]
     */
    private function getCategoriesFromQuery(QueryInterface $queryExpression)
    {
        /** @var BoolExpression $queryExpression */
        $categoryIds = $this->getCategoryIdsFromQuery($queryExpression);
        $categories = [];

        foreach ($categoryIds as $categoryId) {
            try {
                $categories[] = $this->categoryRepository
                    ->get($categoryId, $this->storeManager->getStore()->getId());
            } catch (NoSuchEntityException $e) {
                // do nothing if category is not found by id
            }
        }

        return $categories;
    }

    /**
     * Get Category Ids from search query.
     *
     * Get Category Ids from Must and Should search queries.
     *
     * @param QueryInterface $queryExpression
     * @return array
     */
    private function getCategoryIdsFromQuery(QueryInterface $queryExpression)
    {
        $queryFilterArray = [];
        /** @var BoolExpression $queryExpression */
        $queryFilterArray[] = $queryExpression->getMust();
        $queryFilterArray[] = $queryExpression->getShould();
        $categoryIds = [];

        foreach ($queryFilterArray as $item) {
            if (!empty($item) && isset($item['category'])) {
                $queryFilter = $item['category'];
                /** @var \Magento\Framework\Search\Request\Query\Filter $queryFilter */
                $values = $queryFilter->getReference()->getValue();
                if (is_array($values)) {
                    $categoryIds = array_merge($categoryIds, $values['in']);
                } else {
                    $categoryIds[] = $values;
                }
            }
        }
        return $categoryIds;
    }
}