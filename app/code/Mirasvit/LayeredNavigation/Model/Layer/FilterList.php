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



namespace Mirasvit\LayeredNavigation\Model\Layer;

use Magento\Catalog\Model\Layer\FilterList as CatalogFilterList;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFiltersConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFilterOptionsInterface;

class FilterList extends CatalogFilterList
{
    /**
     * @var bool
     */
    protected $isHorizontal;

    protected $filterTypes = [
        self::CATEGORY_FILTER  => Filter\Category::class,
        self::ATTRIBUTE_FILTER => Filter\Attribute::class,
        self::PRICE_FILTER     => Filter\Price::class,
        self::DECIMAL_FILTER   => Filter\Decimal::class,
    ];

    public function __construct(
        ObjectManagerInterface $objectManager,
        FilterableAttributeListInterface $filterableAttributes,
        AdditionalFiltersConfigInterface $additionalFiltersConfig,
        StoreManagerInterface $storeManager,
        HorizontalFiltersConfigInterface $horizontalFiltersConfig,
        $isHorizontal = false,
        array $filters = []
    ) {
        parent::__construct($objectManager, $filterableAttributes, $filters);

        $this->isHorizontal = $isHorizontal;
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager = $storeManager;
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
    }


    /**
     * Retrieve list of filters
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array|Filter\AbstractFilter[]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if (!count($this->filters)) {
            $this->filters = [
                $this->objectManager->create($this->filterTypes[self::CATEGORY_FILTER], ['layer' => $layer]),
            ];

            foreach ($this->filterableAttributes->getList() as $attribute) {
                $this->filters[] = $this->createAttributeFilter($attribute, $layer);
            }
            $additionalFilters = $this->getAdditionalFilters($layer);
            $this->applyFilterPosition($additionalFilters);

            if ($this->isHorizontal) {
                $this->deleteIgnoredFilter();
            } else {
                $this->deleteHorizontalFilter();
            }
        }

        return $this->filters;
    }

    /**
     * @return bool
     */
    private function deleteHorizontalFilter()
    {
        $horizontalFiltersConfig = $this->horizontalFiltersConfig->getHorizontalFilters(
            $this->storeManager->getStore()->getId()
        );

        if (!$horizontalFiltersConfig) {
            return true;
        }

        if ($horizontalFiltersConfig == HorizontalFilterOptionsInterface::ALL_FILTERED_ATTRIBUTES) {
            $this->filters = [];
        }

        if ($horizontalFiltersConfig) {
            foreach ($this->filters as $key => $filter) {
                if (array_search($filter->getRequestVar(), $horizontalFiltersConfig) !== false) {
                    unset($this->filters[$key]);
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function deleteIgnoredFilter()
    {
        $horizontalFiltersConfig = $this->horizontalFiltersConfig->getHorizontalFilters(
            $this->storeManager->getStore()->getId()
        );

        if ($horizontalFiltersConfig == HorizontalFilterOptionsInterface::ALL_FILTERED_ATTRIBUTES) {
            return true;
        }

        if (!$horizontalFiltersConfig) {
            $this->filters = [];
        }

        if ($horizontalFiltersConfig) {
            foreach ($this->filters as $key => $filter) {
                if (array_search($filter->getRequestVar(), $horizontalFiltersConfig) === false) {
                    unset($this->filters[$key]);
                }
            }
        }

        return true;
    }

    /**
     * @param array $additionalFilters
     * @return bool
     */
    private function applyFilterPosition($additionalFilters)
    {
        if (!$additionalFilters) {
            return true;
        }

        foreach ($additionalFilters as $data) {
            foreach ($data as $position => $additionalFilter) {
                if (isset($this->filters[$position]) && $position != 0) {
                    $firstFilterPart = array_slice($this->filters, 0, $position);
                    $secondFilterPart = array_slice($this->filters, $position);
                    $this->filters = array_merge($firstFilterPart, [$additionalFilter], $secondFilterPart);
                } elseif ($position == 0) {
                    array_unshift($this->filters, $additionalFilter);
                } else {
                    $this->filters = array_merge($this->filters, [$additionalFilter]);
                }
            }
        }

        return true;
    }

    /**
     * @todo encapsulate additional filters adding by injecting them.
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array
     */
    private function getAdditionalFilters($layer)
    {
        $additionalFilters = [];
        $storeId = $this->storeManager->getStore()->getStoreId();

        if ($this->additionalFiltersConfig->isNewFilterEnabled($storeId)) {
            $additionalFilters[][$this->additionalFiltersConfig->getNewFilterPosition($storeId)] =
                $this->objectManager->create(\Mirasvit\LayeredNavigation\Model\Layer\Filter\NewFilter::class,
                    ['layer' => $layer]
                );
        }

        if ($this->additionalFiltersConfig->isOnSaleFilterEnabled($storeId)) {
            $additionalFilters[][$this->additionalFiltersConfig->getOnSaleFilterPosition($storeId)] =
                $this->objectManager->create(\Mirasvit\LayeredNavigation\Model\Layer\Filter\OnSaleFilter::class,
                    ['layer' => $layer]
                );
        }

        if ($this->additionalFiltersConfig->isStockFilterEnabled($storeId)) {
            $additionalFilters[][$this->additionalFiltersConfig->getStockFilterPosition($storeId)] =
                $this->objectManager->create(\Mirasvit\LayeredNavigation\Model\Layer\Filter\StockFilter::class,
                    ['layer' => $layer]
                );
        }

        if ($this->additionalFiltersConfig->isRatingFilterEnabled($storeId)) {
            $additionalFilters[][$this->additionalFiltersConfig->getRatingFilterPosition($storeId)] =
                $this->objectManager->create(\Mirasvit\LayeredNavigation\Model\Layer\Filter\RatingFilter::class,
                    ['layer' => $layer]
                );
        }

        return $additionalFilters;
    }

}
