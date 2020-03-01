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

use Mirasvit\Brand\Api\Config\MoreFromBrandConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class MoreFromBrandConfig extends BaseConfig implements MoreFromBrandConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
         return $this->scopeConfig->getValue(
            'brand/more_products_from_brand/isEnabled',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            'brand/more_products_from_brand/Title',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsLimit()
    {
        return $this->scopeConfig->getValue(
            'brand/more_products_from_brand/ProductsLimit',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
