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



namespace Mirasvit\AllProducts\Config;

use Mirasvit\AllProducts\Api\Config\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config implements ConfigInterface
{
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeId = $storeManager->getStore()->getStoreId();
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'all_products/general/isEnabled',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllProductsUrl()
    {
        $allProductsUrl =  $this->scopeConfig->getValue(
            'all_products/general/AllProductsUrl',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        return ($allProductsUrl) ? : 'all';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            'all_products/general/Title',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->scopeConfig->getValue(
            'all_products/general/MetaTitle',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeyword()
    {
        return $this->scopeConfig->getValue(
            'all_products/general/MetaKeyword',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(
            'all_products/general/MetaDescription',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowAllCategories()
    {
        return $this->scopeConfig->getValue(
            'all_products/general/isShowAllCategories',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * @return int
     */
    public function getMeta()
    {
        return (int) $this->scopeConfig->getValue('all_products/seo/robots', ScopeInterface::SCOPE_STORE);
    }
}
