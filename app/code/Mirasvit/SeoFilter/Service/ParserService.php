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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SeoFilter\Service;

use Mirasvit\SeoFilter\Api\Service\ParserServiceInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\SeoFilter\Api\Data\PriceRewriteInterface;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Mirasvit\SeoFilter\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoFilter\Api\Repository\PriceRewriteRepositoryInterface;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;
use Mirasvit\SeoFilter\Api\Service\LnServiceInterface;

class ParserService implements ParserServiceInterface
{
    /**
     * @var array
     */
    protected static $isPriceFilter = null;

    public function __construct(
        RequestHttp $request,
        StoreManagerInterface $storeManager,
        UrlRewriteCollectionFactory $urlRewrite,
        RewriteRepositoryInterface $rewriteRepository,
        PriceRewriteRepositoryInterface $priceRewriteRepository,
        UrlHelper $urlHelper,
        LnServiceInterface $lnService
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->urlRewrite = $urlRewrite;
        $this->urlHelper = $urlHelper;
        $this->rewriteRepository = $rewriteRepository;
        $this->priceRewriteRepository = $priceRewriteRepository;
        $this->lnService = $lnService;
    }

    /**
     * @return bool|array
     */
    public function parseFilterInformationFromRequest() {
        $params = [];
        $storeId = $this->storeManager->getStore()->getId();
        $requestString = trim($this->request->getPathInfo(), '/');
        $requestPathRewrite = $this->urlRewrite->create()->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('request_path',$requestString);

        if ($requestPathRewrite->getSize() > 0) {
            return false;
        }

        $shortRequestString = substr($requestString, 0, strrpos($requestString, '/'));

        if ($suffix =  $this->urlHelper->getCategoryUrlSuffix()) {
            $shortRequestString = $shortRequestString . $suffix;
        }

        $rewriteItem = $this->urlRewrite->create()->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('request_path',$shortRequestString)
            ->getFirstItem();

        $categoryId = $rewriteItem->getEntityId();

        if (!$categoryId) {
            return false;
        }

        $filterString = $this->getFilterString($requestString, $suffix);

        $rewriteCollection = $this->rewriteRepository->getCollection()
            ->addFieldToFilter(RewriteInterface::REWRITE, $filterString)
            ->addFieldToFilter(RewriteInterface::STORE_ID, $storeId);

        if ($this->isPriceFilter()) {
            $rewriteCollection->addFieldToFilter(RewriteInterface::ATTRIBUTE_CODE,
                ["neq" => RewriteInterface::PRICE]
            );
        }

        $filterInfo = [$filterString];

        if ($rewriteCollection->getSize() == 0) {
            $filterInfo = $this->getPreparedFilterInfo($filterString);
            $rewriteCollection = $this->rewriteRepository->getCollection()
                ->addFieldToFilter(RewriteInterface::REWRITE, ['in' => $filterInfo])
                ->addFieldToFilter(RewriteInterface::STORE_ID, $storeId);
            if ($this->isPriceFilter()) {
                $rewriteCollection->addFieldToFilter(RewriteInterface::ATTRIBUTE_CODE,
                    ["neq" => RewriteInterface::PRICE]
                );
            } else {
                $priceRewriteCollection = $this->priceRewriteRepository->getCollection()
                    ->addFieldToFilter(PriceRewriteInterface::REWRITE, ['in' => $filterInfo])
                    ->addFieldToFilter(PriceRewriteInterface::STORE_ID, $storeId);
            }
        }

        //ln slider compatibility
        $filterData = $this->getFilterData($rewriteCollection->getSize(), $filterInfo);
        $dynamicFilter = $filterData[self::DYNAMIC_FILTER];
        $filterInfo = $filterData[self::FILTER_INFO];

        //ln stock filter compatibility
        $additionalFilterDataStock = $this->getAdditionalFilterDataStock($rewriteCollection->getSize(), $filterInfo);
        $dynamicAdditionalFilterStock = $additionalFilterDataStock[self::DYNAMIC_FILTER];
        $filterInfo = $additionalFilterDataStock[self::FILTER_INFO];

        //ln rating filter compatibility
        $additionalFilterDataRating = $this->getAdditionalFilterDataRating($rewriteCollection->getSize(), $filterInfo);
        $dynamicAdditionalFilterRating = $additionalFilterDataRating[self::DYNAMIC_FILTER];
        $filterInfo = $additionalFilterDataRating[self::FILTER_INFO];

        //ln sale filter compatibility
        $additionalFilterDataSale = $this->getAdditionalFilterDataSale($rewriteCollection->getSize(), $filterInfo);
        $dynamicAdditionalFilterSale = $additionalFilterDataSale[self::DYNAMIC_FILTER];
        $filterInfo = $additionalFilterDataSale[self::FILTER_INFO];

        //ln new product filter compatibility
        $additionalFilterDataNew = $this->getAdditionalFilterDataNew($rewriteCollection->getSize(), $filterInfo);
        $dynamicAdditionalFilterNew = $additionalFilterDataNew[self::DYNAMIC_FILTER];
        $filterInfo = $additionalFilterDataNew[self::FILTER_INFO];

        if (($rewriteCollection->getSize() == count($filterInfo))
            || (isset($priceRewriteCollection)
                && ($priceRewriteCollection->getSize() + $rewriteCollection->getSize()) == count($filterInfo))) {
                foreach ($rewriteCollection as $rewrite) {
                    if (!isset($params[$rewrite->getAttributeCode()])) {
                        $params[$rewrite->getAttributeCode()] = [];
                    }

                    $paramsValue = ($rewrite->getAttributeCode() == RewriteInterface::PRICE)
                        ? $rewrite->getPriceOptionId() : $rewrite->getOptionId();

                    if ($params[$rewrite->getAttributeCode()]) { //multiselect
                        $params[$rewrite->getAttributeCode()] =  $params[$rewrite->getAttributeCode()]
                            . RewriteInterface::MULTISELECT_SEPARATOR . $paramsValue;
                    } else {
                        $params[$rewrite->getAttributeCode()] =  $paramsValue;
                    }
                }
                if (isset($priceRewriteCollection)) {
                    foreach ($priceRewriteCollection as $rewrite) {
                        if (!isset($params[$rewrite->getAttributeCode()])) {
                            $params[$rewrite->getAttributeCode()] = [];
                        }

                        $paramsValue = ($rewrite->getAttributeCode() == RewriteInterface::PRICE)
                            ? $rewrite->getPriceOptionId() : $rewrite->getOptionId();

                        if ($params[$rewrite->getAttributeCode()]) { //multiselect
                            $params[$rewrite->getAttributeCode()] =  $params[$rewrite->getAttributeCode()]
                                . RewriteInterface::MULTISELECT_SEPARATOR . $paramsValue;
                        } else {
                            $params[$rewrite->getAttributeCode()] =  $paramsValue;
                        }
                    }
                }
        } else {
            return false;
        }

        //ln slider compatibility
        if ($dynamicFilter) {
            foreach ($dynamicFilter as $dynamicFilterKey => $dynamicFilterValue) {
                $params[$dynamicFilterKey] = str_replace($dynamicFilterKey, '', $dynamicFilterValue);
            }
        }

        //ln stock compatibility
        if ($dynamicAdditionalFilterStock) {
            $params = $this->getFilterParams(
                $dynamicAdditionalFilterStock,
                $params,
                LnServiceInterface::STOCK_FILTER_FRONT_PARAM
            );
        }

        //ln rating filter compatibility
        if ($dynamicAdditionalFilterRating) {
            $params = $this->getFilterParams(
                $dynamicAdditionalFilterRating,
                $params,
                LnServiceInterface::RATING_FILTER_FRONT_PARAM
            );
        }

        //ln sale filter compatibility
        if ($dynamicAdditionalFilterSale) {
            $params = $this->getFilterParams(
                $dynamicAdditionalFilterSale,
                $params,
                LnServiceInterface::ON_SALE_FILTER_FRONT_PARAM
            );
        }

        //ln new product filter compatibility
        if ($dynamicAdditionalFilterNew) {
            $params = $this->getFilterParams(
                $dynamicAdditionalFilterNew,
                $params,
                LnServiceInterface::NEW_FILTER_FRONT_PARAM
            );
        }

        $parsedResult = [
            RewriteInterface::CATEGORY_ID => $categoryId,
            RewriteInterface::PARAMS => $params
        ];

        return $parsedResult;
    }

    /**
     * @return bool
     */
    protected function isPriceFilter()
    {
        if (self::$isPriceFilter === null) {
            $isPriceFilter = false;
            if (($lnSliderOptions = $this->lnService->getLnSliderOptions())
                && is_array($lnSliderOptions)
                && in_array(RewriteInterface::PRICE, $lnSliderOptions)
            ) {
                $isPriceFilter = true;
            }
            self::$isPriceFilter = $isPriceFilter;
        }

        return self::$isPriceFilter;
    }

    /**
     * Get filter params
     *
     * @param array $dynamicAdditionalFilter
     * @param array $params
     * @param string $filterFrontParam
     * @return array
     */
    protected function getFilterParams($dynamicAdditionalFilter, $params, $filterFrontParam)
    {
        foreach ($dynamicAdditionalFilter
                 as $dynamicAdditionalFilterKey => $dynamicAdditionalFilterValue) {
            if (isset($params[$filterFrontParam])) {
                $params[$filterFrontParam]
                    .= RewriteInterface::MULTISELECT_SEPARATOR . $dynamicAdditionalFilterValue;
            } else {
                $params[$filterFrontParam] = $dynamicAdditionalFilterValue;
            }
        }

        return $params;
    }

    /**
     * Stock filter compatibility
     *
     * @param int $rewriteCollectionSize
     * @param array $filterInfo
     * @return array
     */
    protected function getAdditionalFilterDataStock($rewriteCollectionSize, $filterInfo)
    {
        $dynamicFilter = [];
        if ($rewriteCollectionSize != count($filterInfo)
            && ($this->lnService->isLnStockFilterEnabled())) {
            $stockFilter = [1 => LnServiceInterface::STOCK_FILTER_IN_STOCK_LABEL,
                2 => LnServiceInterface::STOCK_FILTER_OUT_OF_STOCK_LABEL
            ];

            return $this->getPreparedFilterData($stockFilter, $filterInfo);
        }

        return [self::DYNAMIC_FILTER => $dynamicFilter, self::FILTER_INFO => $filterInfo];
    }

    /**
     * Rating filter compatibility
     *
     * @param int $rewriteCollectionSize
     * @param array $filterInfo
     * @return array
     */
    protected function getAdditionalFilterDataRating($rewriteCollectionSize, $filterInfo)
    {
        $dynamicFilter = [];
        if ($rewriteCollectionSize != count($filterInfo)
            && ($this->lnService->isLnRatingFilterEnabled())) {
                $ratingFilter = [1 => LnServiceInterface::RATING_FILTER_ONE_LABEL,
                    2 => LnServiceInterface::RATING_FILTER_TWO_LABEL,
                    3 => LnServiceInterface::RATING_FILTER_THREE_LABEL,
                    4 => LnServiceInterface::RATING_FILTER_FOUR_LABEL,
                    5 => LnServiceInterface::RATING_FILTER_FIVE_LABEL
                ];

                return $this->getPreparedFilterData($ratingFilter, $filterInfo);
        }

        return [self::DYNAMIC_FILTER => $dynamicFilter, self::FILTER_INFO => $filterInfo];
    }

    /**
     * Sale filter compatibility
     *
     * @param int $rewriteCollectionSize
     * @param array $filterInfo
     * @return array
     */
    protected function getAdditionalFilterDataSale($rewriteCollectionSize, $filterInfo)
    {
        $dynamicFilter = [];
        if ($rewriteCollectionSize != count($filterInfo)
            && ($this->lnService->isLnRatingFilterEnabled())) {
            $saleFilter = [1 => LnServiceInterface::ON_SALE_FILTER_FRONT_PARAM];

            return $this->getPreparedFilterData($saleFilter, $filterInfo);
        }

        return [self::DYNAMIC_FILTER => $dynamicFilter, self::FILTER_INFO => $filterInfo];
    }

    /**
     * New products filter compatibility
     *
     * @param int $rewriteCollectionSize
     * @param array $filterInfo
     * @return array
     */
    protected function getAdditionalFilterDataNew($rewriteCollectionSize, $filterInfo)
    {
        $dynamicFilter = [];
        if ($rewriteCollectionSize != count($filterInfo)
            && ($this->lnService->isLnRatingFilterEnabled())) {
            $newFilter = [1 => LnServiceInterface::NEW_FILTER_FRONT_PARAM];

            return $this->getPreparedFilterData($newFilter, $filterInfo);
        }

        return [self::DYNAMIC_FILTER => $dynamicFilter, self::FILTER_INFO => $filterInfo];
    }

    /**
     * Rating filter compatibility
     *
     * @param int $rewriteCollectionSize
     * @param array $filterInfo
     * @return array
     */
    private function getPreparedFilterData($filter, $filterInfo)
    {
        $dynamicFilter = [];
        foreach ($filter as $filterKey => $filterLabel) {
            foreach ($filterInfo as $filterInfoKey => $filterInfoValue) {
                if ($filterInfoValue == $filterLabel) {
                    $dynamicFilter[$filterLabel] = $filterKey;
                    unset($filterInfo[$filterInfoKey]);
                }
            }
        }

        return [self::DYNAMIC_FILTER => $dynamicFilter, self::FILTER_INFO => $filterInfo];
    }

    /**
     * Ln slider compatibility
     *
     * @param int $rewriteCollectionSize
     * @param array $filterInfo
     * @return array
     */
    protected function getFilterData($rewriteCollectionSize, $filterInfo)
    {
        $dynamicFilter = [];
        if ($rewriteCollectionSize != count($filterInfo)
            && ($sliderOptions = $this->lnService->getLnSliderOptions())) {
            foreach ($sliderOptions as $option) {
                foreach ($filterInfo as $key => $filterValue) {
                    if (strpos($filterValue, $option) !== false) {
                        $dynamicFilter[$option] = $filterValue;
                        unset($filterInfo[$key]);
                    }
                }
            }
        }

        return [self::DYNAMIC_FILTER => $dynamicFilter, self::FILTER_INFO => $filterInfo];
    }

    /**
     * @param string $requestString
     * @param string $suffix
     * @return string
     */
    protected function getFilterString($requestString, $suffix)
    {
        $filterString = substr($requestString, strrpos($requestString, '/') + 1);

        if ($suffix && substr($filterString, -strlen($suffix)) === $suffix) {
            $filterString = substr($filterString, 0, -strlen($suffix));
        }

        return $filterString;
    }

    /**
     * @param string $filterString
     * @return array
     */
    protected function getPreparedFilterInfo($filterString)
    {
        //multiselect compatibility
        if (strpos($filterString, RewriteInterface::PRICE) !== false
            && strpos($filterString, RewriteInterface::FILTER_SEPARATOR . RewriteInterface::PRICE) === false
            && (strpos($filterString, RewriteInterface::MULTISELECT_SEPARATOR) !== false
                || (array_search(RewriteInterface::PRICE, $this->lnService->getLnSliderOptions()) !== false))
            ) {
                $filterInfo = explode(RewriteInterface::MULTISELECT_SEPARATOR, $filterString);
                foreach ($filterInfo as $key => $value) {
                    if (strpos($value, 'price') === false) {
                        $filterInfo[$key] = 'price' . $value;
                    }
                }
                return $filterInfo;
        }

        preg_match('/(.*?)' . RewriteInterface::FILTER_SEPARATOR . RewriteInterface::PRICE  . '/ims',
            $filterString,
            $matches
        );

        if (isset($matches[1])) {
            $filterStringFirstPart = $matches[1];
            $filterInfo = explode(RewriteInterface::FILTER_SEPARATOR, $filterStringFirstPart);

            array_push($filterInfo,
                str_replace($filterStringFirstPart . RewriteInterface::FILTER_SEPARATOR, '', $filterString)
            );

            //multiselect compatibility
            if (count($filterInfo) > 1 && isset($filterInfo[count($filterInfo)-1])
                && strpos($filterInfo[count($filterInfo)-1], 'price') !== false
                && strpos($filterInfo[count($filterInfo)-1], RewriteInterface::MULTISELECT_SEPARATOR) !== false) {
                    $priceValues = explode(RewriteInterface::MULTISELECT_SEPARATOR, $filterInfo[count($filterInfo)-1]);
                    foreach ($priceValues as $key => $value) {
                        if ( strpos($value, 'price') === false) {
                            $priceValues[$key] = 'price' . $priceValues[$key];
                        }
                    }

                    if (count($priceValues) > 1 ) {
                        unset($filterInfo[count($filterInfo)-1]);
                        $filterInfo = array_merge($filterInfo, $priceValues);
                    }
            }
        } elseif (($priceString = $this->getPriceString($filterString))) {
            $filterStringPrepared = str_replace($priceString, '', $filterString);
            $filterInfo = explode(RewriteInterface::FILTER_SEPARATOR, $filterStringPrepared);
            $filterInfo[] = $priceString;
        } else {
            $filterInfo = explode(RewriteInterface::FILTER_SEPARATOR, $filterString);
        }

        $filterInfo = array_diff($filterInfo, ['', NULL, false]);

        return $filterInfo;
    }

    /**
     * @param string $filterString
     * @return string
     */
    protected function getPriceString($filterString)
    {
        $filterStringExploded = explode(RewriteInterface::FILTER_SEPARATOR, $filterString);
        $priceKey = false;
        foreach ($filterStringExploded as $key => $value) {
            if (strpos($value, 'price') !== false) {
                $priceKey = $key;
                break;
            }
        }
        if ($priceKey === false || $priceKey === null) {
            return false;
        }
        $priceArray = array_splice($filterStringExploded, $priceKey);

        return implode('-', $priceArray);
    }
}