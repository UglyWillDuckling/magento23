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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Service\Config;

use Mirasvit\SeoFilter\Api\Config\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @param RewriteServiceInterface $rewrite
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isEnabled($store)
    {
        return $this->scopeConfig->getValue(
            'seofilter/seofilter/is_seofilter_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getComplexFilterNamesSeparator($store = null)
    {
        return $this->scopeConfig->getValue(
            'seofilter/seofilter/complex_seofilter_names_separator',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

}
