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

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as EntityAttributeCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as EntityAttributeOptionCollectionFactory;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Mirasvit\SeoFilter\Api\Data\PriceRewriteInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\SeoFilter\Api\Repository\PriceRewriteRepositoryInterface;
use Mirasvit\SeoFilter\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoFilter\Api\Service\FilterLabelServiceInterface;
use Mirasvit\SeoFilter\Api\Service\LnServiceInterface;
use Mirasvit\SeoFilter\Api\Service\RewriteServiceInterface;

class RewriteService implements RewriteServiceInterface
{
    /**
     * @var array
     */
    protected static $activeFilters = null;

    /**
     * @var int
     */
    protected $addition;

    public function __construct(
        StoreManagerInterface $storeManager,
        EntityAttributeCollectionFactory $entityAttributeCollection,
        EntityAttributeOptionCollectionFactory $attributeOptionCollection,
        RewriteRepositoryInterface $rewriteRepository,
        LayerResolver $layerResolver,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        FilterLabelServiceInterface $filterLabelService,
        UrlInterface $urlBuilder,
        PriceRewriteRepositoryInterface $priceRewriteRepository,
        UrlRewriteCollectionFactory $urlRewrite,
        RequestHttp $request
    ) {
        $this->storeManager              = $storeManager;
        $this->storeId                   = $this->storeManager->getStore()->getId();
        $this->entityAttributeCollection = $entityAttributeCollection;
        $this->attributeOptionCollection = $attributeOptionCollection;
        $this->rewriteRepository         = $rewriteRepository;
        $this->layerResolver             = $layerResolver;
        $this->_productFactory           = $productFactory;
        $this->filterLabelService        = $filterLabelService;
        $this->urlBuilder                = $urlBuilder;
        $this->priceRewriteRepository    = $priceRewriteRepository;
        $this->urlRewrite                = $urlRewrite;
        $this->request                   = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRewriteForFilterOption($attributeCode, $attributeId, $optionId)
    {
        if ($attributeCode == PriceRewriteInterface::PRICE) {
            if (is_array($optionId)) {
                $optionId = implode(RewriteInterface::FILTER_SEPARATOR, $optionId);
            }

            $rewrite = $this->priceRewriteRepository->getCollection()
                ->addFieldToFilter(PriceRewriteInterface::ATTRIBUTE_CODE, $attributeCode)
                ->addFieldToFilter(PriceRewriteInterface::PRICE_OPTION_ID, $optionId)
                ->addFieldToFilter(PriceRewriteInterface::STORE_ID, $this->storeId)
                ->getFirstItem();
        } else {
            $rewrite = $this->rewriteRepository->getCollection()
                ->addFieldToFilter(RewriteInterface::ATTRIBUTE_CODE, $attributeCode)
                ->addFieldToFilter(RewriteInterface::OPTION_ID, $optionId)
                ->addFieldToFilter(RewriteInterface::STORE_ID, $this->storeId)
                ->getFirstItem();
        }

        if ($rewrite && is_object($rewrite) && $rewrite->getId()) {
            return $rewrite->getRewrite();
        }

        $rewrite = $this->generateNewRewrite($attributeCode, $attributeId, $optionId);

        return $rewrite;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveFilters()
    {
        if (self::$activeFilters === null) {
            $layer         = $this->layerResolver->get();
            $activeFilters = $layer->getState()->getFilters();
            foreach ($activeFilters as $item) {
                $optionId      = $item->getValue();
                $filter        = $item->getFilter();
                $attributeCode = false;

                switch ($filter->getRequestVar()) {
                    case LnServiceInterface::STOCK_FILTER_FRONT_PARAM:
                        $attributeCode          = LnServiceInterface::STOCK_FILTER_FRONT_PARAM;
                        $rewriteForFilterOption = $this->getStockRewriteForFilterOption($item->getValue());
                        break;
                    case LnServiceInterface::RATING_FILTER_FRONT_PARAM:
                        $attributeCode          = LnServiceInterface::RATING_FILTER_FRONT_PARAM;
                        $rewriteForFilterOption = $this->getRatingRewriteForFilterOption($item->getValue());
                        break;
                    case LnServiceInterface::ON_SALE_FILTER_FRONT_PARAM:
                        $attributeCode          = LnServiceInterface::ON_SALE_FILTER_FRONT_PARAM;
                        $rewriteForFilterOption = $this->getSaleRewriteForFilterOption();
                        break;
                    case LnServiceInterface::NEW_FILTER_FRONT_PARAM:
                        $attributeCode          = LnServiceInterface::NEW_FILTER_FRONT_PARAM;
                        $rewriteForFilterOption = $this->getNewRewriteForFilterOption();
                        break;
                    default:
                        if ($filter->getData('attribute_model')) {
                            $attributeId   = $filter->getAttributeModel()->getAttributeId();
                            $attributeCode = $filter->getAttributeModel()->getAttributeCode();

                            //multiselect
                            if (is_string($optionId)
                                && strpos($optionId, RewriteInterface::MULTISELECT_SEPARATOR) !== false) {
                                $rewriteForFilterOptionMultiselect = '';
                                foreach (explode(RewriteInterface::MULTISELECT_SEPARATOR, $optionId) as $optionIdExploded) {
                                    $rewriteForFilterOptionMultiselect
                                        .= RewriteInterface::FILTER_SEPARATOR . $this->getRewriteForFilterOption(
                                            $attributeCode, $attributeId, $optionIdExploded
                                        );
                                }
                                $rewriteForFilterOption = ltrim($rewriteForFilterOptionMultiselect,
                                    RewriteInterface::FILTER_SEPARATOR
                                );
                            } else {
                                $rewriteForFilterOption = $this->getRewriteForFilterOption(
                                    $attributeCode, $attributeId, $optionId
                                );
                            }
                        }
                }

                if (!$attributeCode) {
                    continue;
                }

                if (isset(self::$activeFilters[$attributeCode])
                    && self::$activeFilters[$attributeCode]) {
                    self::$activeFilters[$attributeCode] = self::$activeFilters[$attributeCode]
                        . RewriteInterface::FILTER_SEPARATOR . $rewriteForFilterOption;
                } else {
                    self::$activeFilters[$attributeCode] = $rewriteForFilterOption;
                }
            }
        }

        return (self::$activeFilters === null) ? [] : self::$activeFilters;
    }

    /**
     * @param string $stockValue
     * @return string
     */
    public function getStockRewriteForFilterOption($stockValue)
    {
        return ($stockValue == 1)
            ? LnServiceInterface::STOCK_FILTER_IN_STOCK_LABEL
            : ((is_array($stockValue)) ? LnServiceInterface::STOCK_FILTER_OUT_OF_STOCK_LABEL
                . RewriteInterface::FILTER_SEPARATOR . LnServiceInterface::STOCK_FILTER_IN_STOCK_LABEL
                : LnServiceInterface::STOCK_FILTER_OUT_OF_STOCK_LABEL);
    }

    /**
     * @param string $ratingValue
     * @return string
     */
    public function getRatingRewriteForFilterOption($ratingValue)
    {
        switch ($ratingValue) {
            case 1:
                $rewriteForFilterOption = LnServiceInterface::RATING_FILTER_ONE_LABEL;
                break;
            case 2:
                $rewriteForFilterOption = LnServiceInterface::RATING_FILTER_TWO_LABEL;
                break;
            case 3:
                $rewriteForFilterOption = LnServiceInterface::RATING_FILTER_THREE_LABEL;
                break;
            case 4:
                $rewriteForFilterOption = LnServiceInterface::RATING_FILTER_FOUR_LABEL;
                break;
            case 5:
                $rewriteForFilterOption = LnServiceInterface::RATING_FILTER_FIVE_LABEL;
                break;
            default:
                if (is_array($ratingValue)) {
                    $rewriteForFilterOption = '';
                    $ratingValuePrepared    = [];
                    $currentUrl             = $this->urlBuilder->getCurrentUrl();
                    foreach ($ratingValue as $value) {
                        $ratingValuePrepared[strpos($currentUrl,
                            LnServiceInterface::RATING_FILTER_FRONT_PARAM . $value)]
                            = $value;
                    }
                    ksort($ratingValuePrepared);
                    foreach ($ratingValuePrepared as $value) {
                        $rewriteForFilterOption
                            .= RewriteInterface::FILTER_SEPARATOR . $this->getRatingRewriteForFilterOption($value);
                    }
                    $rewriteForFilterOption = ltrim($rewriteForFilterOption, RewriteInterface::FILTER_SEPARATOR);
                } else {
                    $rewriteForFilterOption = AdditionalFiltersConfigInterface::RATING_FILTER_FIVE_LABEL;
                }
        }

        return $rewriteForFilterOption;
    }

    /**
     * @return string
     */
    public function getSaleRewriteForFilterOption()
    {
        return LnServiceInterface::ON_SALE_FILTER_FRONT_PARAM;
    }

    /**
     * @return string
     */
    public function getNewRewriteForFilterOption()
    {
        return LnServiceInterface::NEW_FILTER_FRONT_PARAM;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNewRewrite($attributeCode, $attributeId, $optionId)
    {
        if (!(int)$optionId && $attributeCode != RewriteInterface::PRICE) {
            return false;
        }

        if (!$attributeId) {
            return false;
        }

        $attr            = $this->_productFactory->create()->getResource()->getAttribute($attributeId);
        $optionIdAsArray = [$optionId];

        if (!method_exists($attr->getSource(), 'getSpecificOptions')) {
            return false;
        }
        $option = $attr->getSource()->getSpecificOptions($optionIdAsArray);
        if (count($option) <= 1 && $attributeCode != RewriteInterface::PRICE) {
            return false;
        }

        $item = $this->attributeOptionCollection
            ->create()
            ->setStoreFilter($this->storeId, true)
            ->setIdFilter($optionId)
            ->setAttributeFilter($attributeId)
            ->getFirstItem();

        $entityAttributeCollection = $this->entityAttributeCollection->create()
            ->addFieldToFilter('attribute_id', $attributeId);
        $option                    = $entityAttributeCollection->getFirstItem();

        if (($option->getAttributeId() != $item->getAttributeId()
                && $attributeCode != RewriteInterface::PRICE)
            || $option->getAttributeCode() != $attributeCode) {
            return false;
        }

        $label = $attributeCode . '=' . $this->filterLabelService->getLabel($attributeCode, $optionId, $item->getValue());
        /*Needed to avoid 'Unique constraint violation found' exception*/
        $this->addition = 0;
        $requestString  = trim($this->request->getOriginalPathInfo(), '/');
        $label          = $this->checkLabelForDuplicateRewrite($label, $requestString);


        if ($attributeCode == RewriteInterface::PRICE) {
            $rewrite = $this->priceRewriteRepository->create();
            $rewrite->setAttributeCode($attributeCode)
                ->setPriceOptionId($optionId)
                ->setRewrite($label)
                ->setStoreId($this->storeId);

            $this->priceRewriteRepository->save($rewrite);
        } else {
            $rewrite = $this->rewriteRepository->create();
            $rewrite->setAttributeCode($attributeCode)
                ->setOptionId((int)$optionId)
                ->setRewrite($label)
                ->setStoreId($this->storeId);
            $this->rewriteRepository->save($rewrite);
        }


        return $label;
    }


    /**
     * Check if "rewrite + store_id" combination already exists in mst_seo_filter_rewrite table
     * @param string $label
     * @return string
     */
    protected function checkLabelForDuplicateRewrite($label, $requestString)
    {
        $requestStringPrepared = $requestString . '/' . $label;
        $requestPathRewrite    = $this->urlRewrite->create()->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $this->storeId)
            ->addFieldToFilter('request_path', $requestStringPrepared);
        $rewrite               = $this->rewriteRepository->getCollection()
            ->addFieldToFilter(RewriteInterface::REWRITE, $label)
            ->addFieldToFilter(RewriteInterface::STORE_ID, $this->storeId)
            ->getFirstItem();
        if (($rewrite && is_object($rewrite) && $rewrite->getId() && $rewrite->getRewrite() == $label)
            || ($requestPathRewrite && is_object($requestPathRewrite) && ($requestPathRewrite->getSize() > 0))) {
            return $this->addition == 0 ?
                $this->checkLabelForDuplicateRewrite($label . '_' . ++$this->addition, $requestString) :
                $this->checkLabelForDuplicateRewrite(
                    substr($label, 0, -1) . ++$this->addition,
                    $requestString
                );
        }

        return $label;
    }

}