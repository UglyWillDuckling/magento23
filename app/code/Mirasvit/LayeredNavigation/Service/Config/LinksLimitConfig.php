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
use Magento\Store\Model\ScopeInterface;
use Mirasvit\LayeredNavigation\Api\Config\LinksLimitConfigInterface;

class LinksLimitConfig implements LinksLimitConfigInterface
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
    public function getShowMoreLinks($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/links_limit/show_more_links',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getLinksLimitDisplay($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/links_limit/links_limit_way_display',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getScrollHeight($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/links_limit/scroll_height',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getLessText($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/links_limit/show_more_less_text',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getMoreText($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/links_limit/show_more_more_text',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getSwitchLabelColor($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/links_limit/show_more_color',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
