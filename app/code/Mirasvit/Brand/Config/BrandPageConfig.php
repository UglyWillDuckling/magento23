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

use Mirasvit\Brand\Api\Config\BrandPageConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class BrandPageConfig extends BaseConfig implements BrandPageConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function isShowBrandLogo()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_page/isShowBrandLogo',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowBrandDescription()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_page/isShowBrandDescription',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
