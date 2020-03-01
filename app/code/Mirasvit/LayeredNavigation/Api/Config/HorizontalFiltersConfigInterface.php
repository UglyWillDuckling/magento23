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



namespace Mirasvit\LayeredNavigation\Api\Config;

interface HorizontalFiltersConfigInterface
{
    const STATE_BLOCK_NAME = 'catalog.navigation.state';
    const STATE_SEARCH_BLOCK_NAME = 'catalogsearch.navigation.state';
    const STATE_HORIZONTAL_BLOCK_NAME = 'm.catalog.navigation.horizontal.state';
    const FILTER_BLOCK_NAME = 'm.catalog.navigation.horizontal.renderer';

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string|array
     */
    public function getHorizontalFilters($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getHideHorizontalFiltersValue($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isUseCatalogLeftnavHorisontalNavigation($store = null);
}