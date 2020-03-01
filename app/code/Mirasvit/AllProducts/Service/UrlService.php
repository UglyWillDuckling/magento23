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


namespace Mirasvit\AllProducts\Service;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Framework\DataObject;
use Mirasvit\AllProducts\Api\Config\ConfigInterface;
use Magento\Framework\Registry;
use Mirasvit\AllProducts\Api\Service\UrlServiceInterface;

class UrlService implements UrlServiceInterface
{
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        MagentoUrlInterface $urlManager,
        ConfigInterface $config,
        Registry $registry
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->urlManager = $urlManager;
        $this->config = $config;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRoute()
    {
        return $this->config->getAllProductsUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathInfo)
    {
        $identifier = trim($pathInfo, '/');
        $parts = explode('/', $identifier);

        if ($parts[0] != $this->getBaseRoute()) {
            return false;
        }

        if (count($parts) == 1) {
            $urlKey = $parts[0];
            return new DataObject([
                'module_name' => 'all_products_page',
                'controller_name' => 'index',
                'action_name' => 'index',
                'route_name' => $urlKey,
                'params' => [],
            ]);
        }

        return false;
    }
}