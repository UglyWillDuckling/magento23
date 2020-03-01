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

use Mirasvit\LayeredNavigation\Api\Service\SeoFilterUrlServiceInterface;
use Magento\Framework\Module\Manager;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;
use Mirasvit\SeoFilter\Api\Service\FriendlyUrlServiceInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Theme\Block\Html\Pager as HtmlPager;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EntityAttribute;
use Mirasvit\SeoFilter\Api\Service\RewriteServiceInterface;
use Mirasvit\SeoFilter\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Magento\Framework\UrlInterface;

class SeoFilterUrlService implements SeoFilterUrlServiceInterface
{
    public function __construct(
        Manager $moduleManager,
        UrlHelper $urlHelper,
        FriendlyUrlServiceInterface $friendlyUrlService,
        CategoryRepositoryInterface $categoryRepository,
        HtmlPager $htmlPagerBlock,
        EntityAttribute $eavAttribute,
        RewriteServiceInterface $rewrite,
        RewriteRepositoryInterface $rewriteRepository,
        UrlInterface $urlBuilder
    ) {
        $this->moduleManager = $moduleManager;
        $this->urlHelper = $urlHelper;
        $this->friendlyUrlService = $friendlyUrlService;
        $this->categoryRepository = $categoryRepository;
        $this->htmlPagerBlock = $htmlPagerBlock;
        $this->eavAttribute = $eavAttribute;
        $this->rewrite = $rewrite;
        $this->rewriteRepository = $rewriteRepository;
        $this->urlBuilder = $urlBuilder;
        $this->storeId = $urlHelper->getStoreId();
    }


    /**
     * {@inheritdoc}
     */
    public function getAttributeGetUrl($item, $url)
    {
        if ($item->getFilter()->getRequestVar() == 'cat') {
            $categoryUrl = $this->categoryRepository
                ->get($item->getValue(), $this->storeId)
                ->getUrl();

            return $this->urlHelper->addUrlParams($categoryUrl);
        }

        if ($additionalFiltersUrl = $this->getAdditionalFiltersUrl($item, false)) {
            return $additionalFiltersUrl;
        }

        $filter = $item->getFilter();
        if (empty($filter) || !$filter->getData('attribute_model')) {
            return $this->getAttributeOriginalUrl($item);
        }

        $attributeId = $filter->getAttributeModel()->getAttributeId();
        $attributeCode = $filter->getAttributeModel()->getAttributeCode();
        $optionId = $item->getValue();
        if (!$attributeId || !$attributeCode || !$optionId) {
            return $this->getAttributeOriginalUrl($item);
        }

        $url = $this->friendlyUrlService->getFriendlyUrl($attributeCode, $attributeId, $optionId);

        return $this->urlHelper->addUrlParams($url);

    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return string
     */
    protected function getAttributeOriginalUrl($item)
    {
        $query = [
            $item->getFilter()->getRequestVar() => $item->getValue(),
            // exclude current page from urls
            $this->htmlPagerBlock->getPageVarName() => null,
        ];

        return $this->urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSwatchBuildUrl($originalUrl,$attributeCode,$optionId, $url = false)
    {
        $attributeId = $this->eavAttribute->getIdByCode('catalog_product', $attributeCode);
        if ($attributeId) {
            if (strpos($optionId, ',') !== false) {
                $url = $this->getMultiselectSwatchUrl($attributeCode, $optionId, $attributeId, $url);
            } else {
                $url = $this->friendlyUrlService->getFriendlyUrl($attributeCode, $attributeId, $optionId);
            }
        } else {
            $url = $this->getSwatchOriginalUrl($attributeCode, $optionId);
        }

        return $this->urlHelper->addUrlParams($url);
    }

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @param int $attributeId
     * @param string $url
     * @return string
     */
    private function getMultiselectSwatchUrl($attributeCode, $optionId, $attributeId, $url)
    {
        $activeFilters = $this->rewrite->getActiveFilters();
        $activeCurrentAttributeFilters = $activeFilters[$attributeCode];
        $rewrite = $this->rewriteRepository->getCollection()
            ->addFieldToFilter(RewriteInterface::ATTRIBUTE_CODE, $attributeCode)
            ->addFieldToFilter(RewriteInterface::OPTION_ID, ['in' => explode(',', $optionId)])
            ->addFieldToFilter(RewriteInterface::STORE_ID, $this->storeId)
            ->addFieldToFilter(RewriteInterface::REWRITE,
                ['nin' => explode('-', $activeCurrentAttributeFilters)]
            )->getFirstItem();

        $url = $this->friendlyUrlService->getFriendlyUrl(
            $attributeCode,
            $attributeId,
            $rewrite->getOptionId(),
            $url
        );

        return $url;
    }

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     */
    protected function getSwatchOriginalUrl($attributeCode, $optionId)
    {
        $query = [$attributeCode => $optionId];

        return $this->urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeGetRemoveUrl($item)
    {
        $filter = $item->getFilter();
        if (empty($filter)) {
            return $this->getOriginalRemoveUrl($item);
        }

        if ($additionalFiltersRemoveUrl = $this->getAdditionalFiltersUrl($item, true)) {
            return $additionalFiltersRemoveUrl;
        }

        if (!$filter->getData('attribute_model')) {
            return $this->getOriginalRemoveUrl($item);
        }

        $attributeId =  $filter->getAttributeModel()->getAttributeId();
        $attributeCode = $filter->getAttributeModel()->getAttributeCode();
        $optionId = $item->getValue();
        if (!$attributeId || !$attributeCode || !$optionId) {
            return $this->getOriginalRemoveUrl($item);
        }

        $url = $this->getRemoveMultiselectFriendlyUrl($attributeCode, $attributeId, $optionId, true);

        return $this->urlHelper->addUrlParams($url);

    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @param bool $remove
     * @return bool|string
     */
    protected function getAdditionalFiltersUrl($item, $remove=false)
    {
        $additionalFiltersUrl = false;
        if ($item->getFilter()->getRequestVar() == AdditionalFiltersConfigInterface::STOCK_FILTER_FRONT_PARAM) {
            $seoUrlLabel = $this->rewrite->getStockRewriteForFilterOption($item->getValue());
            $additionalFiltersUrl = ($remove)
                ? $this->createRemoveUrl($seoUrlLabel)
                : $this->createAdditionalFiltersUrl($seoUrlLabel);
        }

        if ($item->getFilter()->getRequestVar() == AdditionalFiltersConfigInterface::RATING_FILTER_FRONT_PARAM) {
            $seoUrlLabel =  $this->rewrite->getRatingRewriteForFilterOption($item->getValue());
            $additionalFiltersUrl = ($remove)
                ? $this->createRemoveUrl($seoUrlLabel) 
                : $this->createAdditionalFiltersUrl($seoUrlLabel);
        }

        if ($item->getFilter()->getRequestVar() == AdditionalFiltersConfigInterface::ON_SALE_FILTER_FRONT_PARAM) {
            $seoUrlLabel =  $this->rewrite->getSaleRewriteForFilterOption();
            $additionalFiltersUrl = ($remove)
                ? $this->createRemoveUrl($seoUrlLabel) 
                : $this->createAdditionalFiltersUrl($seoUrlLabel);
        }

        if ($item->getFilter()->getRequestVar() == AdditionalFiltersConfigInterface::NEW_FILTER_FRONT_PARAM) {
            $seoUrlLabel =  $this->rewrite->getNewRewriteForFilterOption();
            $additionalFiltersUrl = ($remove)
                ? $this->createRemoveUrl($seoUrlLabel) 
                : $this->createAdditionalFiltersUrl($seoUrlLabel);
        }

        return $additionalFiltersUrl;
    }

    protected function createRemoveUrl($removedFilters)
    {
        $activeFilters = implode('/', $this->rewrite->getActiveFilters());
        $currentUrl = preg_replace('/'. preg_quote($activeFilters,
                    '/') . '$/', '', $this->urlBuilder->getCurrentUrl());

        $activeFilters = implode('-', $this->rewrite->getActiveFilters());
        $activeFiltersArray = explode('-', $activeFilters);
        $removedFiltersArray = explode('-', $removedFilters);
        $activeFiltersArray = \array_diff($activeFiltersArray, $removedFiltersArray);
        $activeFilters = implode('-', $activeFiltersArray);

        $currentUrlArray = explode('/', $currentUrl);
        $currentUrlArray[count($currentUrlArray)-1] = $activeFilters;

        return implode('/', $currentUrlArray);
    }

    /**
     * @param string $seoUrlLabel
     * @return string
     */
    protected function createAdditionalFiltersUrl($seoUrlLabel)
    {
        $activeFilters = $this->rewrite->getActiveFilters();
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $currentUrlArray = explode('/', $currentUrl);
        $filters = $currentUrlArray[count($currentUrlArray) -1];
        if ($activeFilters) {
            $filters =  $seoUrlLabel . RewriteInterface::FILTER_SEPARATOR . $filters;
        } else {
            $suffix =  $this->urlHelper->getCategoryUrlSuffix();
            $additionalDataFirstPart  = (strpos($filters, '?') !== false)
                ? substr($filters, 0, strpos($filters, "?")) : '';
            $additionalData = ($additionalDataFirstPart) ? str_replace($additionalDataFirstPart, '', $filters) : '';
            $filters = strtok($filters, '?');
            $filters = ($suffix) ? str_replace($suffix, '', $filters) : $filters;
            $filters =  $filters . '/' . $seoUrlLabel . (($suffix) ? : '') . $additionalData;

        }
        $currentUrlArray[count($currentUrlArray) -1] = $filters;

        return implode('/', $currentUrlArray);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @param string $seoUrlLabel
     * @param string $param
     * @return string
     */
    protected function createAdditionalFiltersRemoveUrl($item, $seoUrlLabel, $param)
    {
        $activeFilters = $this->rewrite->getActiveFilters();
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $currentUrlArray = explode('/', $currentUrl);
        $filters = $currentUrlArray[count($currentUrlArray) -1];

        if (isset($activeFilters[$param])
            && $activeFilters[$param]
            && $this->isAdditionalFilterInUrl($filters, $activeFilters[$param])) {
            $suffix = '';
            if (strpos($filters, '.') !== false) {
                $filtersWithoutSuffix = strtok($filters, '.');
                $suffix = str_replace($filtersWithoutSuffix, '',$filters);
            }
            if (isset($filtersWithoutSuffix)) { 
                $filtersWithoutSuffixArray = explode(RewriteInterface::FILTER_SEPARATOR, $filtersWithoutSuffix);
                $key = array_search(
                    $seoUrlLabel,
                    $filtersWithoutSuffixArray
                );
                unset($filtersWithoutSuffixArray[$key]);
                $filters = implode(RewriteInterface::FILTER_SEPARATOR, $filtersWithoutSuffixArray) . $suffix;
            }    
        } else {
            return $this->getOriginalRemoveUrl($item);
        }


        if (!$filters || ($suffix && ($suffix == $filters))) {
            unset($currentUrlArray[count($currentUrlArray) -1]);
            return implode('/', $currentUrlArray) . $suffix;
        } else {
            $currentUrlArray[count($currentUrlArray) -1] = $filters;
        }

        return implode('/', $currentUrlArray);
    }

    /**
     * @param string $filters
     * @param array $activeFilters
     * @return bool
     */
    protected function isAdditionalFilterInUrl($filters, $activeFilters)
    {
        if (strpos($activeFilters, RewriteInterface::FILTER_SEPARATOR) !== false) {
            $activeFiltersArray = explode(RewriteInterface::FILTER_SEPARATOR, $activeFilters);
            $isAdditionalFilterInUrl = false;
            foreach ($activeFiltersArray as $value) {
                $isAdditionalFilterInUrl = (strpos($filters, $value) !== false) ? true : false;
                if (!$isAdditionalFilterInUrl) {
                    break;
                }
            }
            return $isAdditionalFilterInUrl;
        } elseif (strpos($filters, $activeFilters) !== false) {
            return true;
        }

        return false;
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

        return $this->urlBuilder->getUrl('*/*/*', $params);
    }

    /**
     * Multiselect
     * @param string $attributeCode
     * @param string $attributeId
     * @param string $optionId
     * @param bool $remove
     * @return string
     */
    public function getRemoveMultiselectFriendlyUrl($attributeCode, $attributeId, $optionId, $remove = false)
    {
        $filterUrlArray = [];
        // multiselect + Representation of attributes in filter clear block = In one row
        if (strpos($optionId, ',') !== false) {
            $rewriteForFilterOptionMultiselect = '';
            foreach (explode(',', $optionId) as $optionIdExploded) {
                $rewriteForFilterOptionMultiselect
                    .= RewriteInterface::FILTER_SEPARATOR . $this->rewrite->getRewriteForFilterOption(
                        $attributeCode, $attributeId, $optionIdExploded
                    );
            }
            $filterUrlArray[$attributeCode] = ltrim($rewriteForFilterOptionMultiselect,
                RewriteInterface::FILTER_SEPARATOR
            );
        } else {
            $filterUrlArray[$attributeCode] = $this->rewrite->getRewriteForFilterOption($attributeCode,
                $attributeId,
                $optionId
            );
        }

        $activeFilters = $this->rewrite->getActiveFilters();

        $filterUrlTmpArray = $filterUrlArray;
        $filterUrlArray = array_merge($filterUrlArray, $activeFilters);

        if ($remove && isset($filterUrlArray[$attributeCode])) { //delete filter
            $explodedFilterUrlArray = explode(RewriteInterface::FILTER_SEPARATOR, $filterUrlArray[$attributeCode]);
            foreach ($explodedFilterUrlArray as $key => $explodedFilter) {
                if ($explodedFilter == $filterUrlTmpArray[$attributeCode]
                    // multiselect + Representation of attributes in filter clear block = In one row
                    || (strpos($filterUrlTmpArray[$attributeCode], RewriteInterface::FILTER_SEPARATOR) !== false
                        && in_array($explodedFilter,
                            explode(RewriteInterface::FILTER_SEPARATOR, $filterUrlTmpArray[$attributeCode])))
                ) {
                    unset($explodedFilterUrlArray[$key]);
                }
            }
            $filterUrlArray[$attributeCode] = implode(RewriteInterface::FILTER_SEPARATOR, $explodedFilterUrlArray);
        }

        $filterUrlArray = $this->getPreparedFilterUrlArray($filterUrlArray);

        if ($attributeCode == 'price'
            && isset($filterUrlTmpArray[$attributeCode])
            && $filterUrlTmpArray[$attributeCode]) {
                $filterUrlArray[$attributeCode] = str_replace(
                    $filterUrlTmpArray[$attributeCode], '',
                    $filterUrlArray[$attributeCode]
                );
                $filterUrlArray[$attributeCode] = str_replace(
                    RewriteInterface::FILTER_SEPARATOR . RewriteInterface::FILTER_SEPARATOR,
                    RewriteInterface::FILTER_SEPARATOR,
                    $filterUrlArray[$attributeCode]
                );
                $filterUrlArray[$attributeCode] = trim(
                    $filterUrlArray[$attributeCode],
                    RewriteInterface::FILTER_SEPARATOR
                );
        }
        foreach ($filterUrlArray as $key => $value) {
            $filterUrlArray[$key] = str_replace(
                RewriteInterface::FILTER_SEPARATOR . 'price',
                ',',
                $filterUrlArray[$key]
            );
        }

        $filterUrlArray = array_diff($filterUrlArray, [0, null]); //delete empty values

        $filterUrlString = implode(RewriteInterface::FILTER_SEPARATOR, $filterUrlArray);

        $url = $this->friendlyUrlService->getPreparedCurrentCategoryUrl($filterUrlString);

        return $url;
    }

    /**
     * @param string $filterUrlArray
     * @return array
     */
    protected function getPreparedFilterUrlArray($filterUrlArray)
    {
        ksort($filterUrlArray);
        if (isset($filterUrlArray[RewriteInterface::PRICE])) {
            $price = $filterUrlArray[RewriteInterface::PRICE];
            unset($filterUrlArray[RewriteInterface::PRICE]);
            $filterUrlArray[RewriteInterface::PRICE] = $price;
        }

        return $filterUrlArray;
    }
}
