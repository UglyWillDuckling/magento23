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



namespace Mirasvit\LayeredNavigation\Service\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ConfigInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isAjaxEnabled($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'layerednavigation/general/is_ajax_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isMultiselectEnabled($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'layerednavigation/general/is_multiselect_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getMultiselectDisplayOptions($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/general/multiselect_display_options',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getDisplayOptionsBackgroundColor($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/general/display_options_background_color',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getDisplayOptionsBorderColor($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/general/display_options_border_color',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getDisplayOptionsCheckedLabelColor($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/general/display_options_checked_label_color',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isShowOpenedFilters($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/general/is_show_opened_filters',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCorrectElasticFilterCount($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/general/is_correct_elastic_filter_count',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
