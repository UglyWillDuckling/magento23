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



namespace Mirasvit\LayeredNavigation\Block\Renderer;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\LayeredNavigation\Block\Navigation\FilterRenderer;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\LayeredNavigation\Api\Service\FilterServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\MultiselectDisplayOptionsInterface;
use Mirasvit\LayeredNavigation\Api\Repository\AttributeSettingsRepositoryInterface;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\LayeredNavigation\Api\Service\SliderServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\SliderConfigInterface;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\HighlightConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\LinksLimitConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\LinksLimitWayDisplayOptionsInterface;

class Filter extends FilterRenderer
{
    use ConfigTrait;

    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * @var FilterInterface
     */
    protected static $sliderOptions;

    /**
     * Filter constructor.
     * @param Context $context
     * @param FilterServiceInterface $filterService
     * @param AttributeSettingsRepositoryInterface $attributeSettingsRepository
     * @param Registry $registry
     * @param ObjectManagerInterface $objectManager
     * @param SliderServiceInterface $sliderService
     * @param SliderConfigInterface $sliderConfig
     * @param SeoFilterServiceInterface $seoFilterService
     * @param HighlightConfigInterface $highlightConfig
     * @param LinksLimitConfigInterface $linksLimitConfig
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        FilterServiceInterface $filterService,
        AttributeSettingsRepositoryInterface $attributeSettingsRepository,
        Registry $registry,
        ObjectManagerInterface $objectManager,
        SliderServiceInterface $sliderService,
        SliderConfigInterface $sliderConfig,
        SeoFilterServiceInterface $seoFilterService,
        HighlightConfigInterface $highlightConfig,
        LinksLimitConfigInterface $linksLimitConfig,
        ConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->filterService = $filterService;
        $this->attributeSettingsRepository = $attributeSettingsRepository;
        $this->storeManager = $context->getStoreManager();
        $this->registry = $registry;
        $this->objectManager = $objectManager;
        $this->request = $context->getRequest();
        $this->sliderService = $sliderService;
        $this->sliderConfig = $sliderConfig;
        $this->seoFilterService = $seoFilterService;
        $this->highlightConfig = $highlightConfig;
        $this->linksLimitConfig = $linksLimitConfig;
        $this->config = $config;
        $this->storeId = $this->storeManager->getStore()->getStoreId();
    }

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter)
    {
        $template = $this->getFilterTemplate($filter->getRequestVar());
        $this->setTemplate($template);
        $this->filter = $filter;
        $attributeCode = $this->getAttributeCode($this->filter);
        $this->assign('filter', $filter);
        $this->assign('attributeCode', $attributeCode);
        if ($this->sliderConfig->isAttributeSliderEnabled($this->storeId, $filter->getRequestVar())) {
            $sliderData = $this->filter->getSliderData($this->getSliderUrl(), get_class($this->filter));
            $this->assign('sliderData', $sliderData);
        }

        $html = parent::render($this->filter);


        return $html;
    }

    /**
     * @param string $requestVar
     * @return string
     */
    private function getFilterTemplate($requestVar)
    {
        if ($requestVar == AdditionalFiltersConfigInterface::RATING_FILTER_FRONT_PARAM) {
            $this->assign('ratingFilterData',
                $this->registry->registry(AdditionalFiltersConfigInterface::RATING_FILTER_DATA)
            );
        }

        $template = 'layer/filter.phtml';

        if ($this->sliderService->isSliderEnabled($requestVar)) {
            $template = 'layer/slider.phtml';
        }

        return $template;
    }


    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @param bool $multiselect
     * @return bool
     */
    public function isFilterChecked($filterItem, $multiselect = false)
    {
        return $this->filterService->isFilterChecked($filterItem, $multiselect);
    }


    /**
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function getAttributeCode($filter)
    {
        return $this->filterService->getAttributeCode($filter);
    }

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function getFilterUniqueValue($filter)
    {
        return $this->filterService->getFilterUniqueValue($filter);
    }

    /**
     * @return bool
     */
    public function isStylizedCheckbox()
    {
        $isStylizedCheckbox = true;
        $displayOption = $this->config->getMultiselectDisplayOptions();
        if ($displayOption === MultiselectDisplayOptionsInterface::OPTION_DEFAULT
            || $displayOption === MultiselectDisplayOptionsInterface::OPTION_SIMPLE_CHECKBOX) {
            $isStylizedCheckbox = false;
        }

        return $isStylizedCheckbox;
    }

    /**
     * @return string
     */
    public function getCheckboxClass()
    {
        $class = $this->config->getMultiselectDisplayOptions();

        return $class;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        if ($this->config->getMultiselectDisplayOptions() == MultiselectDisplayOptionsInterface::OPTION_DEFAULT) {
            $style = 'display: none;';
        } else {
            $style = '';
        }

        return $style;
    }

    /**
     * @return string
     */
    public function getImageSettings()
    {
        $collection = $this->attributeSettingsRepository->getCollection();
        $imageSettings = [];
        foreach ($collection->getData() as $settings) {
            $optionData = json_decode($settings[AttributeSettingsInterface::IMAGE_OPTIONS], true);
            $optionFilterText = json_decode($settings[AttributeSettingsInterface::FILTER_TEXT], true);
            $optionDataWholeImage = json_decode($settings[AttributeSettingsInterface::IS_WHOLE_WIDTH_IMAGE], true);

            if ($optionData) {
                foreach ($optionData as $option) {
                    if (isset($option['display']['navigation_file'])
                        && ($optionId = $option['option_id'])
                        && ($url = json_decode($option['display']['navigation_file'], true)[0]['url'])
                    ) {
                        $imageSettings[$settings[AttributeSettingsInterface::ATTRIBUTE_CODE]][$optionId]['url'] = $url;
                        if (isset($optionDataWholeImage[$optionId])) {
                            $imageSettings[$settings[AttributeSettingsInterface::ATTRIBUTE_CODE]][$optionId]['is_whole']
                                = true;
                        }
                    }

                    if (isset($optionFilterText[$optionId])) {
                        $imageSettings[$settings[AttributeSettingsInterface::ATTRIBUTE_CODE]][$optionId]['text']
                            = $optionFilterText[$optionId];
                    }
                }
            }
        }

        return $imageSettings;
    }

    /**
     * @param string $imageSettings
     * @param string $valueString
     * @param string $requestVar
     * @param string $label
     * @return string
     */
    public function getFilterLabel($imageSettings, $valueString, $requestVar, $label)
    {
        if ($imageSettings
            && isset($imageSettings[$requestVar][$valueString]['url'])
            && isset($imageSettings[$requestVar][$valueString]['is_whole'])) {
            return '';
        }

        if ($imageSettings
            && isset($imageSettings[$requestVar][$valueString]['text'])
            && $imageSettings[$requestVar][$valueString]['text']) {
            return $imageSettings[$requestVar][$valueString]['text'];
        }

        return $label;
    }

    /**
     * @param string $label
     * @return string
     */
    public function getImageStyle($label)
    {
        if (!$label) {
            return 'width: 100%';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getShowMoreLinksCount()
    {
        return $this->linksLimitConfig->getShowMoreLinks($this->storeId);
    }

    /**
     * @return string
     */
    public function getLinksLimitDisplay()
    {
        return $this->linksLimitConfig->getLinksLimitDisplay($this->storeId);
    }

    /**
     * @return string
     */
    public function getScrollStyle($filterItemsCount)
    {
        $scrollStyle = '';
        if ($this->getShowMoreLinksCount()
            && $this->getLinksLimitDisplay() == LinksLimitWayDisplayOptionsInterface::OPTION_SCROLL
            && $filterItemsCount > $this->getShowMoreLinksCount()) {
            $scrollHeight = ($this->linksLimitConfig->getScrollHeight($this->storeId))
                ?: $this->getShowMoreLinksCount() * 33;
            $scrollStyle = 'style="overflow-x: hidden; max-height:' . $scrollHeight . 'px"';
        }

        return $scrollStyle;
    }

    /**
     * @return bool
     */
    public function isLinkShowHide()
    {
        if ($this->getLinksLimitDisplay() == LinksLimitWayDisplayOptionsInterface::OPTION_LINK_SHOW_HIDE) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFilterLessText()
    {
        return $this->linksLimitConfig->getLessText($this->storeId);
    }

    /**
     * @return string
     */
    public function getFilterMoreText()
    {
        return $this->linksLimitConfig->getMoreText($this->storeId);
    }

    /**
     * @return string
     */
    public function isNavMultiselectEnabled()
    {
        return ConfigTrait::isMultiselectEnabled();
    }

    /**
     * @return string
     */
    public function isNavAjaxEnabled()
    {
        return $this->isAjaxEnabled();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }

    /**
     * @return string
     */
    public function getSliderUrl()
    {
        return $this->sliderService->getSliderUrl($this->filter, $this->getSliderParamTemplate());
    }

    /**
     * @return string
     */
    public function getSliderParamTemplate()
    {
        return $this->sliderService->getParamTemplate($this->filter);
    }

    /**
     * @return string
     */
    public function isSeoFilterEnabled()
    {
        return $this->seoFilterService->isUseSeoFilter();
    }

    /**
     * @return string
     */
    public function isEnabledLinkHighlight()
    {
        return $this->highlightConfig->isEnabledLinkHighlight($this->storeId);
    }
}
