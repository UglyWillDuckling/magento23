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



namespace Mirasvit\LayeredNavigation\Service;

use Mirasvit\LayeredNavigation\Api\Service\CssCreatorServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFiltersConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\SliderConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\HighlightConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\LinksLimitConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFilterOptionsInterface;

/**
 * Class CssCreatorService
 * @package Mirasvit\LayeredNavigation\Service
 *
 * Generate css depending from configuration
 */
class CssCreatorService implements CssCreatorServiceInterface
{
    /**
     * CssCreatorService constructor.
     * @param AdditionalFiltersConfigInterface $additionalFiltersConfig
     * @param HorizontalFiltersConfigInterface $horizontalFiltersConfig
     * @param SliderConfigInterface $sliderConfig
     * @param HighlightConfigInterface $highlightConfig
     * @param LinksLimitConfigInterface $linksLimitConfig
     * @param ConfigInterface $config
     * @param FilterClearBlockConfigInterface $filterClearBlockConfig
     */
    public function __construct(
        AdditionalFiltersConfigInterface $additionalFiltersConfig,
        HorizontalFiltersConfigInterface $horizontalFiltersConfig,
        SliderConfigInterface $sliderConfig,
        HighlightConfigInterface $highlightConfig,
        LinksLimitConfigInterface $linksLimitConfig,
        ConfigInterface $config,
        FilterClearBlockConfigInterface $filterClearBlockConfig
    ) {
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
        $this->sliderConfig = $sliderConfig;
        $this->highlightConfig = $highlightConfig;
        $this->linksLimitConfig = $linksLimitConfig;
        $this->config = $config;
        $this->filterClearBlockConfig = $filterClearBlockConfig;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getCssContent($storeId)
    {
        $css = '';
        $css = $this->getSliderCss($storeId, $css);
        $css = $this->getHorizontalFiltersCss($storeId, $css);
        $css = $this->getHighlightColorCss($storeId, $css);
        $css = $this->getFilterClearBlockCss($storeId, $css);
        $css = $this->getLinksLimitCss($storeId, $css);
        $css = $this->getDisplayOptionsCss($storeId, $css);
        $css = $this->getShowOpenedFiltersCss($storeId, $css);

        return $css;
    }

    /**
     * @param int $storeId
     * @param string $css
     * @return string
     */
    private function getSliderCss($storeId, $css)
    {
        if ($this->sliderConfig->getSliderOptions($storeId)) {
            if ($this->sliderConfig->getSliderHandleColor($storeId)) {
                $css .= '.m-navigation-slider .ui-slider-handle {background:'
                    . $this->sliderConfig->getSliderHandleColor($storeId) . ' !important;} ';
            }
            if ($this->sliderConfig->getSliderHandleBorderColor($storeId)) {
                $css .= '.m-navigation-slider .ui-slider-handle {border:'
                    . $this->sliderConfig->getSliderHandleBorderColor($storeId) . ' !important;} ';
            }
            if ($this->sliderConfig->getSliderConnectColor($storeId)) {
                $css .= '.m-navigation-slider .ui-slider {background:'
                    . $this->sliderConfig->getSliderConnectColor($storeId) . ' !important;} ';
            }
            if ($this->sliderConfig->getSliderTextColor($storeId)) {
                $css .= '.m-navigation-slider input[class^="amount-m-navigation-slider-"] {color:'
                    . $this->sliderConfig->getSliderTextColor($storeId) . ' !important;} ';
            }
        }

        return $css;
    }

    /**
     * @param int $storeId
     * @param string $css
     * @return string
     */
    private function getHorizontalFiltersCss($storeId, $css)
    {
        if ($hideHorizontalFiltersValue = $this->horizontalFiltersConfig->getHideHorizontalFiltersValue($storeId)) {
            $hideHorizontalFiltersValue = str_replace('px', '', $hideHorizontalFiltersValue); //delete px if exist
            $css .= '/* Hide horizontal filters if screen size less then (px) - begin */';
            $css .= '@media all and (max-width: ' . $hideHorizontalFiltersValue . 'px) {';
            $css .= '.navigation-horizontal .block-subtitle.filter-subtitle {display: none !important;} ';
            $css .= '.navigation-horizontal .filter-options {display: none !important;} ';
            $css .= '} ';
            $css .= '/* Hide horizontal filters if screen size less then (px) - end */';
        }

        if ($this->horizontalFiltersConfig->getHorizontalFilters($storeId)) {
            $css .= '/* Show horizontal clear filter panel - begin */';
            $css .= '.navigation-horizontal {display: block;} ';
            $css .= '.navigation-horizontal .block-subtitle.filter-subtitle {display: block} ';
            $css .= '.navigation-horizontal .filter-options {display: block} ';
            $css .= '/* Show horizontal clear filter panel - end */ ';
        }

        //show only horizontal filters
        if ($this->horizontalFiltersConfig->getHorizontalFilters($storeId)
            == HorizontalFilterOptionsInterface::ALL_FILTERED_ATTRIBUTES) {
                $css .= '/* Show only horizontal filters - begin */';
                $css .= '.navigation-horizontal .filter-options {display:block !important;} ';
                $css .= '.sidebar.sidebar-main .block-title {display:none!important;} ';
                $css .= '.sidebar.sidebar-additional {display:none!important;} ';
                $css .= '.columns .column.main {width: 100%;} ';
                $css .= 'form[m-navigation-filter="RatingFilter"] .filter-options-content a {margin: 0 !important; 
                padding: 0 !important;} ';
                $css .= '/* Show only horizontal filters - end */ ';
        }

        return $css;
    }

    /**
     * @param int $storeId
     * @param string $css
     * @return string
     */
    private function getFilterClearBlockCss($storeId, $css)
    {
        if ($this->filterClearBlockConfig->isHorizontalFiltersClearPanelEnabled($storeId)) {
            $css .= '/* Show horizontal clear filter panel - begin */';
            $css .= '.navigation-horizontal {display: block !important;} ';
            $css .= '@media all and (mix-width: 767px) {';
            $css .= '.navigation-horizontal .block-actions.filter-actions {display: block !important;} ';
            $css .= '} ';
            $css .= '@media all and (max-width: 767px) {';
            $css .= '.navigation-horizontal .block-title.filter-title {display: none !important;} ';
            $css .= '} ';
            $css .= '.sidebar .block-actions.filter-actions {display: none;} ';
            $css .= '/* Show horizontal clear filter panel - end */';
        } else {
            $css .= '.navigation-horizontal .block-actions.filter-actions {display: none;} ';
        }

        return $css;
    }

    /**
     * @param int $storeId
     * @param string $css
     * @return string
     */
    private function getHighlightColorCss($storeId, $css)
    {
        $color = $this->highlightConfig->getHighlightColor($storeId);

        if (!$color) {
            $color = HighlightConfigInterface::DEFAULT_HIGHLIGHT_COLOR;
        }
        $css .= '.item .m-navigation-link-highlight { color:' . $color . '; } ';
        $css .= '.m-navigation-highlight-swatch .swatch-option.selected { outline: 2px solid ' . $color . '; } ';
        $css .= '.m-navigation-filter-item .swatch-option.image:not(.disabled):hover { outline: 2px solid'
            . $color . '; border: 1px solid #fff; } ';
        $css .= '.swatch-option.image.m-navigation-highlight-swatch { outline: 2px solid'
            . $color . '; 1px solid #fff; } ';
        $css .= '.m-navigation-swatch .swatch-option:not(.disabled):hover { outline: 2px solid'
            . $color . '; border: 1px solid #fff;  color: #333; } ';
        $css .= '.m-navigation-swatch .m-navigation-highlight-swatch .swatch-option { outline: 2px solid'
            . $color . '; border: 1px solid #fff;  color: #333; } ';


        return $css;
    }

    /**
     * @param int $storeId
     * @param string $css
     * @return string
     */
    private function getLinksLimitCss($storeId, $css)
    {
        $color = $this->linksLimitConfig->getSwitchLabelColor($storeId);

        if (!$color) {
            $color = LinksLimitConfigInterface::DEFAULT_LINKS_LIMIT_COLOR;
        }
        $css .= 'span[class^="more-links-m-navigation-show-more-"] { color:' . $color . '; } ';

        return $css;
    }

    /**
     * @param int $storeId
     * @param string $css
     * @return string
     */
    private function getDisplayOptionsCss($storeId, $css)
    {
        if ($backgroundColor = $this->config->getDisplayOptionsBackgroundColor($storeId)) {
            $css .= '.checkbox input[type="checkbox"]:checked + label::before,
                      .checkbox input[type="radio"]:checked + label::before { background-color:'
                . $backgroundColor . '; } ';
        }
        if ($borderColor = $this->config->getDisplayOptionsBorderColor($storeId)) {
            $css .= '.checkbox input[type="checkbox"]:checked + label::before,
                      .checkbox input[type="radio"]:checked + label::before { border-color:'
                . $borderColor . '; } ';
        }
        if ($checkedLabelColor = $this->config->getDisplayOptionsCheckedLabelColor($storeId)) {
            $css .= '.checkbox input[type="checkbox"]:checked+label::after, 
                     .checkbox input[type="radio"]:checked+label::after { color:'
                . $checkedLabelColor . '; } ';
        }

        return $css;
    }

    /**
     * @param int $storeId
     * @param string $css
     * @return string
     */
    private function getShowOpenedFiltersCss($storeId, $css)
    {
        if ($isShowOpenedFilters = $this->config->isShowOpenedFilters($storeId)) {
            $css .= '.sidebar .filter-options .filter-options-content { display: block; } ';
        }

        return $css;
    }
}