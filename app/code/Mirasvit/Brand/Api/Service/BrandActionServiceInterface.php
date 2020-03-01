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



namespace Mirasvit\Brand\Api\Service;

interface BrandActionServiceInterface
{
    CONST SEPARATOR = '_';
    CONST BRAND_INDEX_ACTION = 'brand_index_index';
    CONST BRAND_VIEW_ACTION = 'brand_brand_view';
    CONST BRAND_FULL_ACTION = ['brand/brand/view/', 'brand/brand/view'];

    /**
     * @return string
     */
    public function getFullActionName();

    /**
     * @return bool
     */
    public function isBrandViewPage();

    /**
     * @return bool
     */
    public function isBrandIndexPage();

    /**
     * @return bool
     */
    public function isBrandPage();
}