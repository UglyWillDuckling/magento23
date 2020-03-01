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

use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider;
use Magento\Framework\Search\Request\Dimension;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterNewServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterOnSaleServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterStockServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterRatingServiceInterface;
use Magento\Store\Model\StoreManagerInterface;

class AdditionalFiltersSelect
{
    public function __construct(
        FilterNewServiceInterface $filterNewService,
        FilterOnSaleServiceInterface $filterOnSaleService,
        FilterStockServiceInterface $filterStockService,
        FilterRatingServiceInterface $filterRatingService,
        AdditionalFiltersConfigInterface $additionalFiltersConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->filterNewService = $filterNewService;
        $this->filterOnSaleService = $filterOnSaleService;
        $this->filterStockService = $filterStockService;
        $this->filterRatingService = $filterRatingService;
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param DataProvider $subject
     * @param \Closure $proceed
     * @param BucketInterface $bucket
     * @param Dimension[] $dimensions
     * @param Table $entityIdsTable
     * @return Select
     */
    public function aroundGetDataSet(
        DataProvider $subject,
        \Closure $proceed,
        BucketInterface $bucket,
        array $dimensions,
        Table $entityIdsTable
    ) {
        if ($bucket->getField() == AdditionalFiltersConfigInterface::NEW_FILTER
            && ($currentScope = $this->filterNewService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isNewFilterEnabled($currentScope)
        ) {

            return $this->filterNewService->createNewFilterSelect($currentScope, $entityIdsTable);
        }

        if ($bucket->getField() == AdditionalFiltersConfigInterface::ON_SALE_FILTER
            && ($currentScope = $this->filterNewService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isOnSaleFilterEnabled($currentScope)
        ) {
            return $this->filterOnSaleService->createOnSaleFilterSelect($currentScope, $entityIdsTable);
        }

        if ($bucket->getField() == AdditionalFiltersConfigInterface::STOCK_FILTER
            && ($currentScope = $this->filterStockService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isStockFilterEnabled($currentScope)
        ) {
            return $this->filterStockService->createStockFilterSelect($currentScope, $entityIdsTable);
        }

        if ($bucket->getField() == AdditionalFiltersConfigInterface::RATING_FILTER
            && ($currentScope = $this->filterRatingService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isRatingFilterEnabled($currentScope)
        ) {
            return $this->filterRatingService->createRatingFilterSelect($currentScope, $entityIdsTable);
        }

        return $proceed($bucket, $dimensions, $entityIdsTable);
    }

}