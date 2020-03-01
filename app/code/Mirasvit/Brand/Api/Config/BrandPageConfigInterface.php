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



namespace Mirasvit\Brand\Api\Config;

interface BrandPageConfigInterface
{
    CONST BRAND_ATTRIBUTE = 'BrandAttribute';
    CONST ATTRIBUTE_OPTION_ID = 'AttributeOptionId';
    CONST BRAND_URL_KEY = 'BrandUrlKey';
    CONST BRAND_DEFAULT_NAME = 'BrandDefaultName';
    CONST BRAND_DATA = 'm__BrandData';
    CONST BRAND_PAGE_ID = 'BrandPageId';

    CONST INDEX_FOLLOW = 'INDEX, FOLLOW';
    CONST NOINDEX_FOLLOW = 'NOINDEX, FOLLOW';
    CONST INDEX_NOFOLLOW = 'INDEX, NOFOLLOW';
    CONST NOINDEX_NOFOLLOW = 'NOINDEX, NOFOLLOW';

    CONST BANNER_AFTER_TITLE_POSITION = 'After title position';
    CONST BANNER_BEFORE_DESCRIPTION_POSITION = 'Before description position';
    CONST BANNER_AFTER_DESCRIPTION_POSITION = 'After description position';

    CONST BANNER_AFTER_TITLE_POSITION_LAYOUT = 'm.brand.banner.after_title';
    CONST BANNER_BEFORE_DESCRIPTION_POSITION_LAYOUT = 'm.brand.banner.before_description';
    CONST BANNER_AFTER_DESCRIPTION_POSITION_LAYOUT = 'm.brand.banner.after_description';

    /**
     * @return bool
     */
    public function isShowBrandLogo();

    /**
     * @return bool
     */
    public function isShowBrandDescription();

}