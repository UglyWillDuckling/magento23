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



namespace Mirasvit\LayeredNavigation\Api\Config;

interface AdditionalFiltersConfigInterface
{
    const NEW_FILTER = 'new_products';
    const ON_SALE_FILTER = 'on_sale';
    const STOCK_FILTER = 'stock_status';
    const IN_STOCK_FILTER = 1;
    const OUT_OF_STOCK_FILTER = 2;
    const RATING_FILTER = 'rating_summary';

    const NEW_FILTER_FRONT_PARAM = 'new_products';
    const ON_SALE_FILTER_FRONT_PARAM = 'on_sale';
    const STOCK_FILTER_FRONT_PARAM = 'stock';
    const RATING_FILTER_FRONT_PARAM = 'rating';

    const NEW_FILTER_DEFAULT_LABEL = 'New';
    const ON_SALE_FILTER_DEFAULT_LABEL = 'Sale';
    const STOCK_FILTER_DEFAULT_LABEL = 'Stock';
    const RATING_FILTER_DEFAULT_LABEL = 'Rating';

    const RATING_FILTER_DATA = 'm__rating_filter_data';
    const RATING_DATA = [
        5 => 100,
        4 => 80,
        3 => 60,
        2 => 40,
        1 => 20
    ];

    const STOCK_FILTER_IN_STOCK_LABEL = 'instock';
    const STOCK_FILTER_OUT_OF_STOCK_LABEL = 'outofstock';

    const RATING_FILTER_ONE_LABEL = 'rating1';
    const RATING_FILTER_TWO_LABEL = 'rating2';
    const RATING_FILTER_THREE_LABEL = 'rating3';
    const RATING_FILTER_FOUR_LABEL = 'rating4';
    const RATING_FILTER_FIVE_LABEL = 'rating5';

    // New Filter
    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isNewFilterEnabled($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getNewFilterLabel($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getNewFilterPosition($store = null);

    // On Sale Filter
    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isOnSaleFilterEnabled($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getOnSaleFilterLabel($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getOnSaleFilterPosition($store = null);

    // Stock Filter
    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isStockFilterEnabled($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getStockFilterLabel($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getInStockFilterLabel($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getOutOfStockFilterLabel($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getStockFilterPosition($store = null);

    // Rating Filter
    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isRatingFilterEnabled($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getRatingFilterLabel($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getRatingFilterPosition($store = null);
}