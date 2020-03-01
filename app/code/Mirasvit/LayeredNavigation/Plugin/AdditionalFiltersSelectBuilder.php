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

use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterStockServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterRatingServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterNewServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterOnSaleServiceInterface;
use Magento\Framework\App\RequestInterface;

class AdditionalFiltersSelectBuilder
{
    public function __construct(
        AdditionalFiltersConfigInterface $additionalFiltersConfig,
        StoreManagerInterface $storeManager,
        FilterStockServiceInterface $filterStockService,
        FilterRatingServiceInterface $filterRatingService,
        FilterNewServiceInterface $filterNewService,
        FilterOnSaleServiceInterface $filterOnSaleService,
        RequestInterface $request
    ) {
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager = $storeManager;
        $this->filterStockService = $filterStockService;
        $this->filterRatingService = $filterRatingService;
        $this->filterNewService = $filterNewService;
        $this->filterOnSaleService = $filterOnSaleService;
        $this->storeId = $storeManager->getStore()->getId();
        $this->request = $request;
    }

    /**
     * Build index query
     *
     * @param \Magento\CatalogSearch\Model\Search\IndexBuilder $subject
     * @return \Magento\Framework\DB\Select $select
     */
    public function afterBuild($subject, $select)
    {
        if ($this->additionalFiltersConfig->isStockFilterEnabled($this->storeId)) {
            $this->filterStockService->addStockToSelect($select);
        }

        if ($this->additionalFiltersConfig->isRatingFilterEnabled($this->storeId)) {
            $this->filterRatingService->addRatingToSelect($select);
        }

        if ($this->additionalFiltersConfig->isNewFilterEnabled($this->storeId)) {
            $this->filterNewService->addNewToSelect($select,
                $this->storeId,
                $this->request->getParam(AdditionalFiltersConfigInterface::NEW_FILTER_FRONT_PARAM)
            );
        }

        if ($this->additionalFiltersConfig->isOnSaleFilterEnabled($this->storeId)) {
            $this->filterOnSaleService->addOnSaleToSelect($select,
                $this->storeId,
                $this->request->getParam(AdditionalFiltersConfigInterface::ON_SALE_FILTER_FRONT_PARAM)
            );
        }

        return $select;
    }
}
