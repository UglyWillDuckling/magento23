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



namespace Mirasvit\Brand\Config;

use Mirasvit\Brand\Api\Config\GeneralConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class GeneralConfig extends BaseConfig implements GeneralConfigInterface
{
    const DEFAULT_ALL_BRAND_URL = 'brand';

    const XML_PATH_BRAND_URL_SUFFIX = 'brand/general/url_suffix';

    const BRAND_URL_SUFFIX_CATEGORY_ON = 1;
    const BRAND_URL_SUFFIX_CATEGORY_OFF = 2;

    /**
     * {@inheritdoc}
     */
    public function getBrandAttribute()
    {
        return $this->scopeConfig->getValue(
            'brand/general/BrandAttribute',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllBrandUrl()
    {
        $allBrandUrl = $this->scopeConfig->getValue(
            'brand/general/AllBrandUrl',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        $allBrandUrl = $allBrandUrl ?: self::DEFAULT_ALL_BRAND_URL;

        return $allBrandUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatBrandUrl()
    {
        return (int) $this->scopeConfig->getValue(
            'brand/general/FormatBrandUrl',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlSuffix()
    {
        if ($this->isCategoryUrlSuffix()) {
            return $this->getCategoryUrlSuffix();
        } else {
            return $this->scopeConfig->getValue(self::XML_PATH_BRAND_URL_SUFFIX);
        }
    }

    /**
     * @inheritdoc
     */
    public function isCategoryUrlSuffix()
    {
        return ((int) $this->scopeConfig->getValue(self::XML_PATH_BRAND_URL_SUFFIX . '_category'))
            === self::BRAND_URL_SUFFIX_CATEGORY_ON;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandLinkPosition()
    {
        return $this->scopeConfig->getValue(
            'brand/general/BrandLinkPosition',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandLinkPositionTemplate()
    {
        return $this->scopeConfig->getValue(
            'brand/general/BrandLinkPositionTemplate',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandLinkLabel()
    {
        return $this->scopeConfig->getValue(
            'brand/general/BrandLinkLabel',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowNotConfiguredBrands()
    {
        return $this->scopeConfig->getValue(
            'brand/general/isShowNotConfiguredBrands',
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
            'brand/general/isShowAllCategories',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Retrieve category rewrite suffix for store.
     *
     * @return string
     */
    private function getCategoryUrlSuffix()
    {
       return $this->scopeConfig->getValue(
            \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
