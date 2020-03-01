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



namespace Mirasvit\LayeredNavigation\Model\Layer\Filter;

use Magento\CatalogSearch\Model\Layer\Filter\Decimal as CatalogSearchDecimal;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Api\Service\FilterDataServiceInterface;
use Mirasvit\LayeredNavigation\Service\Config\SliderConfig;
use Mirasvit\LayeredNavigation\Service\SliderService;

/**
 * Layer decimal filter
 */
class Decimal extends CatalogSearchDecimal
{
    use ConfigTrait;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Layer\Filter\Decimal
     */
    private $resource;

    /**
     * @var bool
     */
    private static $isAdded;

    /**
     * @var array
     */
    private $facetedData;

    /**
     * @var bool
     */
    private $isFromToDataAdded;

    /**
     * @var SliderConfig
     */
    private $sliderConfig;

    /**
     * @var SliderService
     */
    private $sliderService;

    /**
     * @var FilterDataServiceInterface
     */
    private $filterDataService;

    /**
     * @var int
     */
    private $storeId;

    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        DecimalFactory $filterDecimalFactory,
        PriceCurrencyInterface $priceCurrency,
        SliderConfig $sliderConfig,
        SliderService $sliderService,
        FilterDataServiceInterface $filterDataService,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $filterDecimalFactory,
            $priceCurrency,
            $data
        );
        $this->resource = $filterDecimalFactory->create();
        $this->priceCurrency = $priceCurrency;
        $this->sliderConfig = $sliderConfig;
        $this->sliderService = $sliderService;
        $this->filterDataService = $filterDataService;
        $this->storeId = $storeManager->getStore()->getStoreId();
    }

    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $filter = $request->getParam($this->getRequestVar());

        if (!empty($filter) && !is_array($filter)) {
            list($from, $to) = explode('-', $filter);
            $this->setFromToData(['from' => $from, 'to' => $to]);
            $this->isFromToDataAdded = true;
        }

        $request->setParam($this->getRequestVar(), $filter);

        $apply = parent::apply($request);

        if ($this->sliderConfig->isAttributeSliderEnabled($this->storeId, $this->getRequestVar())) {
            $facets = $this->getFacetedData();
            if (($minMaxData = $this->getMinMaxData($facets, $this->getRequestVar()))
                && !$this->isFromToDataAdded) {
                $this->setFromToData(['from' => $minMaxData['from'], 'to' => $minMaxData['to']]);
            }

        }

        return $apply;
    }

    protected function getMinMaxData($facets, $requestVar)
    {
        $minMaxData = [];
        $sliderDataKey = $this->sliderService->getSliderDataKey($requestVar);
        if (isset($facets[$sliderDataKey]['min'])
            && isset($facets[$sliderDataKey]['max'])) {
            $minMaxData['from'] = $facets[$sliderDataKey]['min'];
            $minMaxData['to'] = $facets[$sliderDataKey]['max'];
        }

        return $minMaxData;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();

        /** @var \Mirasvit\LayeredNavigation\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();
        $productSize = $productCollection->getSize();

        $facets = $productCollection->getFacetedData($attribute->getAttributeCode());

        $data = [];

        foreach ($facets as $key => $aggregation) {
            if ($key === $this->sliderService->getSliderDataKey($attribute->getAttributeCode())) {
                continue;
            }
            $count = $aggregation['count'];
            if (!$this->isOptionReducesResults($count, $productSize)) {
                continue;
            }
            list($from, $to) = explode('_', $key);
            if ($from == '*') {
                $from = '';
            }
            if ($to == '*') {
                $to = '';
            }
            $label = $this->renderRangeLabel(
                empty($from) ? 0 : $from,
                empty($to) ? 0 : $to
            );

            $value = $from . '-' . $to;

            $data[] = [
                'label' => $label,
                'value' => $value,
                'count' => $count,
                'from'  => $from,
                'to'    => $to,
            ];
        }

        return $data;
    }

    /**
     * Get fiter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        $itemsCount = parent::getItemsCount();

        if ($itemsCount == 0
            && ($data = $this->getFromToData())
            && $data['from'] != $data['to']) {
            $itemsCount = 1;
        }

        return $itemsCount;
    }

    /**
     * Prepare text of range label
     *
     * @param float|string $from
     * @param float|string $to
     * @return \Magento\Framework\Phrase
     */
    protected function renderRangeLabel($from, $to)
    {
        if ($this->getAttributeModel()->getFrontendInput() === 'text') {
            if ($to === '' || intval($to) === 0) {
                return __('%1 and above', $from);
            } else {
                return __('%1 - %2', $from, $to);
            }
        } else {
            $formattedFromPrice = $this->priceCurrency->format($from);
            if ($to === '') {
                return __('%1 and above', $formattedFromPrice);
            } else {
                if ($from != $to
                    && !$this->sliderConfig->isAttributeSliderEnabled($this->storeId, $this->getRequestVar())) {
                    $to -= .01;
                }
                return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($to));
            }
        }
    }

    /**
     * @return array
     */
    public function getSliderData($url, $class)
    {
        return $this->sliderService->getSliderData(
            $this->getFacetedData(),
            $this->getRequestVar(),
            $this->getFromToData(),
            $url,
            $class
        );
    }

    /**
     * @return mixed
     */
    public function getFacetedData()
    {
        if (is_null($this->facetedData)) {
            $productCollection = $this->getLayer()->getProductCollection();
            $attribute = $this->getAttributeModel();

            if (ConfigTrait::isMultiselectEnabled() || $this->sliderService->isSliderEnabled($this->_requestVar)) {
                $requestBuilder = clone $productCollection->getCloneRequestBuilder();

                $requestBuilder->removePlaceholder($attribute->getAttributeCode());
                $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.from');
                $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.to');

                $queryRequest = $requestBuilder->create();
                $facets = $this->filterDataService->getFilterBucketData($queryRequest, $attribute->getAttributeCode());
            } elseif (self::$isAdded) {
                return [];
            } else {
                $facets = $productCollection->getFacetedData($attribute->getAttributeCode());
            }

            $this->facetedData = $facets;
        }

        return $this->facetedData;
    }
}
