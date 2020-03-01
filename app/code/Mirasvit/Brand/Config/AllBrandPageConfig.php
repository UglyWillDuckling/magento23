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

use Mirasvit\Brand\Api\Config\AllBrandPageConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class AllBrandPageConfig extends BaseConfig  implements AllBrandPageConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function isShowBrandLogo()
    {
        return $this->scopeConfig->getValue(
            'brand/all_brand_page/isShowBrandLogo',
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
            'brand/all_brand_page/MetaTitle',
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
            'brand/all_brand_page/MetaKeyword',
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
            'brand/all_brand_page/MetaDescription',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
