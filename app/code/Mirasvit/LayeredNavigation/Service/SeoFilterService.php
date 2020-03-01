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


namespace Mirasvit\LayeredNavigation\Service;

use Mirasvit\LayeredNavigation\Api\Service\SeoFilterServiceInterface;
use Magento\Framework\Module\Manager;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;


class SeoFilterService implements SeoFilterServiceInterface
{
    /**
     * @var bool
     */
    protected static $isUseSeoFilter;

    public function __construct(
        Manager $moduleManager,
        Config $config,
        UrlHelper $urlHelper
    ) {
        $this->moduleManager = $moduleManager;
        $this->config = $config;
        $this->urlHelper = $urlHelper;
        $this->storeId = $urlHelper->getStoreId();
    }

    /**
     * {@inheritdoc}
     */
    public function isUseSeoFilter()
    {
        if (self::$isUseSeoFilter !== null) {
            return self::$isUseSeoFilter;
        }

        self::$isUseSeoFilter = false;
        if ($this->config->isEnabled($this->storeId)
            && $this->urlHelper->isCategoryPage()) {
                self::$isUseSeoFilter = true;
        }


        return self::$isUseSeoFilter;
    }
}