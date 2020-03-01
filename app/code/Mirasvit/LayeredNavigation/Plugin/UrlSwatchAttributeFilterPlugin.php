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



namespace Mirasvit\LayeredNavigation\Plugin;

use Magento\Eav\Model\ResourceModel\Entity\Attribute as EntityAttribute;
use Magento\Framework\UrlInterface;
use Mirasvit\LayeredNavigation\Api\Service\FilterServiceInterface;
use Magento\Theme\Block\Html\Pager as HtmlPager;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Api\Service\UrlServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterUrlServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;

class UrlSwatchAttributeFilterPlugin
{
    use ConfigTrait;

    private $htmlPagerBlock;

    private $filterService;

    private $eavAttribute;

    private $urlBuilder;

    private $url;

    private $urlService;

    private $seoFilterService;

    private $seoFilterUrlService;

    private $filterClearBlockConfig;

    public function __construct(
        UrlInterface $url,
        HtmlPager $htmlPagerBlock,
        FilterServiceInterface $filterService,
        EntityAttribute $eavAttribute,
        UrlInterface $urlBuilder,
        UrlServiceInterface $urlService,
        SeoFilterServiceInterface $seoFilterService,
        SeoFilterUrlServiceInterface $seoFilterUrlService,
        FilterClearBlockConfigInterface $filterClearBlockConfig
    ) {
        $this->htmlPagerBlock = $htmlPagerBlock;
        $this->filterService = $filterService;
        $this->eavAttribute = $eavAttribute;
        $this->urlBuilder = $urlBuilder;
        $this->url = $url;
        $this->urlService = $urlService;
        $this->seoFilterService = $seoFilterService;
        $this->seoFilterUrlService = $seoFilterUrlService;
        $this->filterClearBlockConfig = $filterClearBlockConfig;
    }

    /**
     * @param \Mirasvit\LayeredNavigation\Block\Renderer\Swatch $object
     * @param string $originalUrl
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterBuildUrl($object, $originalUrl, $attributeCode = null, $optionId = null)
    {
        /** @var \Mirasvit\LayeredNavigation\Block\Renderer\Swatch $object */

        if (!$attributeCode) {
            $attributeCode = $object->attributeCode;
            $optionId = $object->optionId;
        }

        $activeFilters = $this->filterService->getActiveFiltersArray();

        if (!$activeFilters || !ConfigTrait::isMultiselectEnabled()) {
            if ($this->seoFilterService->isUseSeoFilter()) {
                return $this->seoFilterUrlService->getSwatchBuildUrl($originalUrl,
                    $attributeCode,
                    $optionId
                );
            }
            return $originalUrl;
        }

        if (ConfigTrait::isMultiselectEnabled() && isset($activeFilters[$attributeCode])) {
            $activeFilterArray = explode(',', $activeFilters[$attributeCode]);
            $key = array_search($optionId, $activeFilterArray);
            if ($key === false) {
                $optionId = $activeFilters[$attributeCode] . ',' . $optionId;
            } else {
                return $this->getSwatchRemoveUrl($optionId);
            }
        }

        $query = [
            $attributeCode                          => $optionId,
            $this->htmlPagerBlock->getPageVarName() => null,
        ];

        $url = $this->url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);

        if ($this->seoFilterService->isUseSeoFilter()) {
            return $this->seoFilterUrlService->getSwatchBuildUrl($originalUrl,
                $attributeCode,
                $optionId
            );
        }

        return $this->urlService->getPreparedUrl($url);
    }


    /**
     * Retrieve Clear Filters URL
     *
     * @param int $optionId
     * @return string
     */
    public function getSwatchRemoveUrl($optionId)
    {
        $filterState = [];
        foreach ($this->filterService->getActiveFilters() as $item) {
            if ($item->getValueString() == $optionId) {
                $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
            } else {
                if ($this->filterClearBlockConfig->isFilterClearBlockInOneRow()
                    && strpos($item->getValueString(), ',') !== false) {
                    $filterState[$item->getFilter()->getRequestVar()]
                        = $this->prepareRequestVar($item->getValueString(), $optionId);
                } else {
                    $filterState[$item->getFilter()->getRequestVar()] = $item->getValueString();
                }
            }
        }
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;

        return $this->urlService->getPreparedUrl($this->urlBuilder->getUrl('*/*/*', $params));
    }

    /**
     * @param string $valueString
     * @param string $optionId
     * @return bool
     */
    private function prepareRequestVar($valueString, $optionId)
    {
        $valueStringArray = explode(',', $valueString);
        $unsetKey = array_search($optionId, $valueStringArray);
        if ($unsetKey !== false && isset($valueStringArray[$unsetKey])) {
            unset($valueStringArray[$unsetKey]);
        }

        $valueString = implode(',', $valueStringArray);

        return $valueString;
    }
}