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

use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager as HtmlPager;
use Mirasvit\LayeredNavigation\Api\Service\FilterServiceInterface;
use Magento\Framework\App\Request\Http;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Mirasvit\LayeredNavigation\Api\Service\UrlServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterUrlServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;

class UrlAttributeFilterPlugin
{
    use ConfigTrait;

    /**
     * UrlAttributeFilterPlugin constructor.
     * @param HtmlPager $htmlPagerBlock
     * @param UrlInterface $url
     * @param FilterServiceInterface $filterService
     * @param Http $request
     * @param Attribute $eavAttribute
     * @param UrlServiceInterface $urlService
     * @param SeoFilterServiceInterface $seoFilterService
     * @param SeoFilterUrlServiceInterface $seoFilterUrlService
     */
    public function __construct(
        HtmlPager $htmlPagerBlock,
        UrlInterface $url,
        FilterServiceInterface $filterService,
        Http $request,
        Attribute $eavAttribute,
        UrlServiceInterface $urlService,
        SeoFilterServiceInterface $seoFilterService,
        SeoFilterUrlServiceInterface $seoFilterUrlService,
        FilterClearBlockConfigInterface $filterClearBlockConfig
    ) {
        $this->eavAttribute = $eavAttribute;
        $this->htmlPagerBlock = $htmlPagerBlock;
        $this->url = $url;
        $this->filterService = $filterService;
        $this->request = $request;
        $this->urlService = $urlService;
        $this->seoFilterService = $seoFilterService;
        $this->seoFilterUrlService = $seoFilterUrlService;
        $this->filterClearBlockConfig = $filterClearBlockConfig;
    }

    /**
     * Get filter item url
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return string
     */
    public function afterGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
        if (!ConfigTrait::isMultiselectEnabled()) {
            return $this->getCorrectGetUrl($item, false, false);
        }

        $itemRequestVar = $item->getFilter()->getRequestVar();
        $itemValue = $item->getValue();
        $activeFilters = $this->filterService->getActiveFiltersArray();

        if (isset($activeFilters[$itemRequestVar])
            && $itemRequestVar != 'price') {
                $itemValue = $this->request->getParam($item->getFilter()->getRequestVar()) . ',' . $itemValue;
        } elseif ($itemRequestVar == 'price'
            && isset($activeFilters[$itemRequestVar])) {
                $filterPriceParams = explode(',', $activeFilters[$itemRequestVar]);
                foreach ($filterPriceParams as $param) {
                    if (strpos($itemValue, $param) === false) {
                        $filterPriceParam = explode('-', $param);
                        $itemValue = $itemValue . ',' . $filterPriceParam[0] . '-'
                            . $filterPriceParam[1];
                    }
                }
        }

        $query = [
            $itemRequestVar => $itemValue,
            $this->htmlPagerBlock->getPageVarName() => null,
        ];

        $url = $this->url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);

        return $this->getCorrectGetUrl($item, $url, true);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @param bool $url
     * @param bool $isMultiselect
     * @return bool|string
     */
    protected function getCorrectGetUrl($item, $url = false, $isMultiselect = false)
    {
        if ($isMultiselect && $this->seoFilterService->isUseSeoFilter()) {
            $url = $this->seoFilterUrlService->getAttributeGetUrl($item, $this->urlService->getPreparedUrl($url));
        } elseif ($isMultiselect) {
            $url = $this->urlService->getPreparedUrl($url);
        } elseif (!$isMultiselect && $this->seoFilterService->isUseSeoFilter()) {
            $url = $this->seoFilterUrlService->getAttributeGetUrl($item, $this->getOriginalUrl($item));
        } elseif (!$isMultiselect) {
            $url = $this->getOriginalUrl($item);
        }

        return $url;
    }

    /**
     * Get url for remove item from filter
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return string
     */
    public function afterGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
        if (!ConfigTrait::isMultiselectEnabled()) {
            return $this->getCorrectGetRemoveUrl($item, false);
        }

        $param = $this->request->getParam($item->getFilter()->getRequestVar());
        $isFilterClearBlockInOneRow = $this->filterClearBlockConfig->isFilterClearBlockInOneRow();

        if ((strpos($param, ',') !== false && !$isFilterClearBlockInOneRow)
            || (strpos($item->getValueString(), ',') === false //use for $isFilterClearBlockInOneRow = true
                && (strpos($param, ',') !== false)
                && $isFilterClearBlockInOneRow)
        ) {
            $paramArray = explode(',', $param);
            if (count($paramArray) > 1) {
                $key = array_search($item->getValueString(), $paramArray);
                unset($paramArray[$key]);
                $param = implode(',', $paramArray);
                $query = [$item->getFilter()->getRequestVar() => $param];
                $params['_current'] = true;
                $params['_use_rewrite'] = true;
                $params['_query'] = $query;
                $params['_escape'] = true;
                $url = $this->url->getUrl('*/*/*', $params);

                return $this->getCorrectGetRemoveUrl($item, true, $url);
            }
        }

        return $this->getCorrectGetRemoveUrl($item, true);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @param bool $isMultiselect
     * @param bool|string $url
     * @return string
     */
    protected function getCorrectGetRemoveUrl($item, $isMultiselect = false, $url = false)
    {
        if ($isMultiselect && $url && $this->seoFilterService->isUseSeoFilter()) {
            $url = $this->seoFilterUrlService->getAttributeGetRemoveUrl($item);
        } elseif ($isMultiselect && $this->seoFilterService->isUseSeoFilter()) {
            $url = $this->seoFilterUrlService->getAttributeGetRemoveUrl($item);
        } elseif ($isMultiselect && $url) {
            $url = $this->urlService->getPreparedUrl($url);
        } elseif ($isMultiselect) {
            $url = $this->urlService->getPreparedUrl($this->getOriginalRemoveUrl($item));
        } elseif (!$isMultiselect && $this->seoFilterService->isUseSeoFilter()) {
            $url = $this->seoFilterUrlService->getAttributeGetRemoveUrl($item);
        } elseif (!$isMultiselect) {
            $url = $this->getOriginalRemoveUrl($item);
        }

        return $url;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return string
     */
    protected function getOriginalUrl($item)
    {
        $query = [
            $item->getFilter()->getRequestVar() => $item->getValue(),
            $this->htmlPagerBlock->getPageVarName() => null,
        ];

        return $this->url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return string
     */
    protected function getOriginalRemoveUrl($item)
    {
        $query = [$item->getFilter()->getRequestVar() => $item->getFilter()->getResetValue()];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;

        return $this->url->getUrl('*/*/*', $params);
    }
}