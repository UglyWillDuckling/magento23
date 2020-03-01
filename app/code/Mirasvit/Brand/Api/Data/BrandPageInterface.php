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



namespace Mirasvit\Brand\Api\Data;

interface BrandPageInterface
{
    const TABLE_NAME = 'mst_brand_page';

    const ID = 'brand_page_id';
    const ATTRIBUTE_OPTION_ID = 'attribute_option_id';
    const ATTRIBUTE_ID = 'attribute_id';
    const IS_ACTIVE = 'is_active';
    const URL_KEY = 'url_key';
    const LOGO = 'logo';
    const BRAND_TITLE = 'brand_title';
    const BRAND_DESCRIPTION = 'brand_description';
    const META_TITLE = 'meta_title';
    const KEYWORD = 'meta_keyword';
    const META_DESCRIPTION = 'meta_description';
    const ROBOTS = 'robots';
    const CANONICAL = 'canonical';
    const BANNER_ALT = 'banner_alt';
    const BANNER_TITLE = 'banner_title';
    const BANNER = 'banner';
    const BANNER_POSITION = 'banner_position';
    const IS_SHOW_IN_BRAND_SLIDER = 'is_show_in_brand_slider';
    const SLIDER_POSITION = 'slider_position';
    const BRAND_SHORT_DESCRIPTION = 'brand_short_description';

    const ATTRIBUTE_CODE = 'attribute_code';
    const BRAND_NAME = 'brand_name';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getAttributeOptionId();

    /**
     * @param string $value
     * @return $this
     */
    public function setAttributeOptionId($value);

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param string $value
     * @return $this
     */
    public function setAttributeId($value);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param string $value
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return int
     */
    public function getLogo();

    /**
     * @param string $value
     * @return $this
     */
    public function setLogo($value);

    /**
     * @return int
     */
    public function getBrandTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setBrandTitle($value);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $value
     * @return $this
     */
    public function setUrlKey($value);

    /**
     * @return int
     */
    public function getBrandDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setBrandDescription($value);

    /**
     * @return int
     */
    public function getMetaTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setMetaTitle($value);

    /**
     * @return int
     */
    public function getKeyword();

    /**
     * @param string $value
     * @return $this
     */
    public function setKeyword($value);

    /**
     * @return int
     */
    public function getMetaDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setMetaDescription($value);

    /**
     * @return int
     */
    public function getRobots();

    /**
     * @param string $value
     * @return $this
     */
    public function setRobots($value);

    /**
     * @return int
     */
    public function getCanonical();

    /**
     * @param string $value
     * @return $this
     */
    public function setCanonical($value);

    //additional
    /**
     * @return string
     */
    public function getAttributeCode();

    /**
     * @return string
     */
    public function getBrandName();

    /**
     * @param string $value
     * @return $this
     */
    public function setBrandName($value);

    /**
     * @return string
     */
    public function getBannerAlt();

    /**
     * @param string $value
     * @return $this
     */
    public function setBannerAlt($value);

    /**
     * @return string
     */
    public function getBannerTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setBannerTitle($value);

    /**
     * @return string
     */
    public function getBanner();

    /**
     * @param string $value
     * @return $this
     */
    public function setBanner($value);

    /**
     * @return string
     */
    public function getBannerPosition();

    /**
     * @param string $value
     * @return $this
     */
    public function setBannerPosition($value);

    /**
     * @return int
     */
    public function getBrandShortDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setBrandShortDescription($value);

}