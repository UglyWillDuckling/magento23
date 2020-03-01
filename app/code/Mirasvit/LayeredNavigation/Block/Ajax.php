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



namespace Mirasvit\LayeredNavigation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\LayeredNavigation\Api\Service\FilterServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFiltersConfigInterface;

class Ajax extends Template
{
    use ConfigTrait;

    /**
     * Ajax constructor.
     * @param Context $context
     * @param FilterServiceInterface $filterService
     * @param ConfigInterface $config
     * @param SeoFilterServiceInterface $seoFilterService
     * @param FilterClearBlockConfigInterface $filterClearBlockConfig
     * @param HorizontalFiltersConfigInterface $horizontalFiltersConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        FilterServiceInterface $filterService,
        ConfigInterface $config,
        SeoFilterServiceInterface $seoFilterService,
        FilterClearBlockConfigInterface $filterClearBlockConfig,
        HorizontalFiltersConfigInterface $horizontalFiltersConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->filterService = $filterService;
        $this->config = $config;
        $this->seoFilterService = $seoFilterService;
        $this->filterClearBlockConfig = $filterClearBlockConfig;
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
        $this->storeId = $context->getStoreManager()->getStore()->getStoreId();
    }

    /**
     * @return string
     */
    public function getCleanUrl()
    {
        $activeFilters = [];

        foreach ($this->filterService->getActiveFilters() as $item) {
            $filter = $item->getFilter();
            $activeFilters[$filter->getRequestVar()] = $filter->getCleanValue();
        }

        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = array_merge($activeFilters,
            [ConfigInterface::AJAX_SUFFIX => null]
        );
        $params['_escape'] = true;

        $url = $this->_urlBuilder->getUrl('*/*/*', $params);
        $url = str_replace('&amp;', '&', $url);

        return $url;
    }

    /**
     * @return string
     */
    public function getOverlayUrl()
    {
        return $this->getViewFileUrl('Mirasvit_LayeredNavigation::images/ajax_loading.gif');
    }

    /**
     * @return string
     */
    public function isSeoFilterEnabled()
    {
        return $this->seoFilterService->isUseSeoFilter();
    }

    /**
     * @return int
     */
    public function isFilterClearBlockInOneRow()
    {
        return $this->filterClearBlockConfig->isFilterClearBlockInOneRow();
    }

    /**
     * @return int
     */
    public function  isUseCatalogLeftnavHorisontalNavigation()
    {
        return $this->horizontalFiltersConfig->isUseCatalogLeftnavHorisontalNavigation($this->storeId);
    }
}
