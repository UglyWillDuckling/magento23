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



namespace Mirasvit\SeoFilter\Plugin;

use Mirasvit\SeoFilter\Helper\Url as UrlHelper;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;

class PagerPlugin
{
    /**
     * @param UrlHelper $urlHelper
     * @param Config $config
     */
    public function __construct(
        UrlHelper $urlHelper,
        Config $config
    ) {
        $this->urlHelper = $urlHelper;
        $this->config = $config;
        $this->storeId = $urlHelper->getStoreId();
    }

    /**
     * Retrieve page URL
     *
     * @param Magento\Theme\Block\Html\Pager $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPageUrl($subject, $result)
    {
        if ($this->config->isEnabled($this->storeId)) {
            return $this->urlHelper->getQueryParams($result);
        }

        return $result;
    }

}