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

interface ConfigInterface
{
    const AJAX_SUFFIX = 'mAjax';
    const AJAX_PRODUCT_LIST_WRAPPER_ID = 'm-navigation-product-list-wrapper';
    const AJAX_STATE_WRAPPER_ID = 'm-navigation-state-wrapper';
    const AJAX_STATE_WRAPPER_CLASS = 'm-navigation-state';
    const AJAX_STATE_WRAPPER_INPUT_CLASS = 'm-navigation-state-input';
    const AJAX_SWATCH_WRAPPER_CLASS = 'm-navigation-swatch';

    const NAV_IMAGE_REG_PRODUCT_DATA = 'm-navigation-register-product-data';

    const NAV_REPLACER_TAG = '<div id="m-navigation-replacer"></div>'; //use for filter opener

    const IS_CATALOG_SEARCH = 'catalogsearch';
    const IS_PRICE_SLIDER_ADDED = 'm__is_price_slider_added';

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isAjaxEnabled($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isMultiselectEnabled($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getMultiselectDisplayOptions($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getDisplayOptionsBackgroundColor($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getDisplayOptionsBorderColor($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getDisplayOptionsCheckedLabelColor($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isShowOpenedFilters($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCorrectElasticFilterCount($store = null);
}