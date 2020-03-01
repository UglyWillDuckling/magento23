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

interface BrandSliderConfigInterface
{
    /**
     * @return int
     */
    public function getItemsLimit();

    /**
     * @return string
     */
    public function getOrder();

    /**
     * @return bool
     */
    public function isShowTitle();

    /**
     * @return string
     */
    public function getTitleText();

    /**
     * @return string
     */
    public function getTitleTextColor();

    /**
     * @return string
     */
    public function getTitleBackgroundColor();

    /**
     * @return bool
     */
    public function isShowBrandLabel();

    /**
     * @return string
     */
    public function getBrandLabelColor();

    /**
     * @return bool
     */
    public function isShowButton();

    /**
     * @return bool
     */
    public function isShowPagination();

    /**
     * @return bool
     */
    public function isAutoPlay();

    /**
     * @return bool
     */
    public function isAutoPlayLoop();

    /**
     * @return int
     */
    public function getAutoPlayInterval();

    /**
     * @return int
     */
    public function getPauseOnHover();

    /**
     * @return int
     */
    public function getSliderWidth();

    /**
     * @return int
     */
    public function getSliderImageWidth();

    /**
     * @return int
     */
    public function getSpacingBetweenImages();

    /**
     * @return string
     */
    public function getInactivePagingColor();

    /**
     * @return string
     */
    public function getActivePagingColor();

    /**
     * @return string
     */
    public function getHoverPagingColor();

    /**
     * @return string
     */
    public function getNavigationButtonsColor();
}