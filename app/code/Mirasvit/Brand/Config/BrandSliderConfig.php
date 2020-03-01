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

use Mirasvit\Brand\Api\Config\BrandSliderConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class BrandSliderConfig extends BaseConfig implements BrandSliderConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItemsLimit()
    {
        $itemsLimit = $this->scopeConfig->getValue(
            'brand/brand_slider/ItemsLimit',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        if (!$itemsLimit) {
            $itemsLimit = 4;
        }

        return $itemsLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/Order',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowTitle()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/isShowTitle',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleText()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/TitleText',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleTextColor()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/TitleTextColor',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleBackgroundColor()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/TitleBackgroundColor',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowBrandLabel()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/isShowBrandLabel',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandLabelColor()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/BrandLabelColor',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowButton()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/isShowButton',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowPagination()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/isShowPagination',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isAutoPlay()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/isAutoPlay',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isAutoPlayLoop()
    {
        $isAutoPlayLoop = $this->scopeConfig->getValue(
            'brand/brand_slider/isAutoPlayLoop',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        if ($isAutoPlayLoop === null) {
            $isAutoPlayLoop = 0;
        }

        return $isAutoPlayLoop;
    }

    /**
     * {@inheritdoc}
     */
    public function getAutoPlayInterval()
    {
        $autoPlayInterval = $this->scopeConfig->getValue(
            'brand/brand_slider/AutoPlayInterval',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        if (!$autoPlayInterval) {
            $autoPlayInterval = 4000;
        }

        return $autoPlayInterval;
    }

    /**
     * {@inheritdoc}
     */
    public function getPauseOnHover()
    {
        $pauseOnHover = $this->scopeConfig->getValue(
            'brand/brand_slider/PauseOnHover',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        if ($pauseOnHover === null) {
            $pauseOnHover = 0;
        }

        return $pauseOnHover;
    }

    /**
     * {@inheritdoc}
     */
    public function getSliderWidth()
    {
        $sliderWidth = $this->scopeConfig->getValue(
            'brand/brand_slider/SliderWidth',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        return (int)$sliderWidth;
    }

    /**
     * {@inheritdoc}
     */
    public function getSliderImageWidth()
    {
        $sliderImageWidth = $this->scopeConfig->getValue(
            'brand/brand_slider/SliderImageWidth',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        return (int)$sliderImageWidth;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpacingBetweenImages()
    {
        $spacingBetweenImages = $this->scopeConfig->getValue(
            'brand/brand_slider/SpacingBetweenImages',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        if (!$spacingBetweenImages) {
            $spacingBetweenImages = 10;
        }

        return (int)$spacingBetweenImages;
    }

    /**
     * {@inheritdoc}
     */
    public function getInactivePagingColor()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/InactivePagingColor',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePagingColor()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/ActivePagingColor',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getHoverPagingColor()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/HoverPagingColor',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNavigationButtonsColor()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_slider/NavigationButtonsColor',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
