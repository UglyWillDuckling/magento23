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
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AdditionalFiltersConfig implements AdditionalFiltersConfigInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    // New Filter
    /**
     * {@inheritdoc}
     */
    public function isNewFilterEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/is_enabled_new_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNewFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/label_new_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNewFilterPosition($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/position_new_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    // On Sale Filter
    /**
     * {@inheritdoc}
     */
    public function isOnSaleFilterEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/is_enabled_on_sale_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOnSaleFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/label_on_sale_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOnSaleFilterPosition($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/position_on_sale_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    // Stock Filter
    /**
     * {@inheritdoc}
     */
    public function isStockFilterEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/is_enabled_stock_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStockFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/label_stock_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInStockFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/label_in_stock_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOutOfStockFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/label_out_of_stock_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStockFilterPosition($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/position_stock_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    // Rating Filter
    /**
     * {@inheritdoc}
     */
    public function isRatingFilterEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/is_enabled_rating_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRatingFilterLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/label_rating_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRatingFilterPosition($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/additional_filters/position_rating_filter',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

}
