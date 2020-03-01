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



namespace Mirasvit\SeoFilter\Api\Service;

interface LnServiceInterface
{
    const STOCK_FILTER_IN_STOCK_LABEL = 'instock';
    const STOCK_FILTER_OUT_OF_STOCK_LABEL = 'outofstock';
    const STOCK_FILTER_FRONT_PARAM = 'stock';

    const RATING_FILTER_FRONT_PARAM = 'rating';
    const RATING_FILTER_ONE_LABEL = 'rating1';
    const RATING_FILTER_TWO_LABEL = 'rating2';
    const RATING_FILTER_THREE_LABEL = 'rating3';
    const RATING_FILTER_FOUR_LABEL = 'rating4';
    const RATING_FILTER_FIVE_LABEL = 'rating5';

    const ON_SALE_FILTER_FRONT_PARAM = 'on_sale';

    const NEW_FILTER_FRONT_PARAM = 'new_products';

    /**
     * @return bool
     */
    public function isLnEnabled();

    /**
     * @return array
     */
    public function getLnSliderOptions();

    /**
     * @return bool
     */
    public function isLnNewFilterEnabled();

    /**
     * @return bool
     */
    public function isLnOnSaleFilterEnabled();

    /**
     * @return bool
     */
    public function isLnStockFilterEnabled();

    /**
     * @return bool
     */
    public function isLnRatingFilterEnabled();
}