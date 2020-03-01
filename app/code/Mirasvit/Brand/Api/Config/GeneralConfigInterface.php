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

interface GeneralConfigInterface
{
    /**
     * @return string
     */
    public function getBrandAttribute();

    /**
     * @return string
     */
    public function getAllBrandUrl();

    /**
     * @return int
     */
    public function getFormatBrandUrl();

    /**
     * @return string
     */
    public function getUrlSuffix();

    /**
     * @return bool
     */
    public function isCategoryUrlSuffix();

    /**
     * @return string
     */
    public function getBrandLinkPosition();

    /**
     * @return array
     */
    public function getBrandLinkPositionTemplate();

    /**
     * @return string
     */
    public function getBrandLinkLabel();

    /**
     * @return bool
     */
    public function isShowNotConfiguredBrands();

    /**
     * @return bool
     */
    public function isShowAllCategories();
}