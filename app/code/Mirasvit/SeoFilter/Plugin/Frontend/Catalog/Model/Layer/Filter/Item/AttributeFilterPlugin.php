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



namespace Mirasvit\SeoFilter\Plugin\Frontend\Catalog\Model\Layer\Filter\Item;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager as HtmlPager;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Mirasvit\SeoFilter\Api\Data\PriceRewriteInterface;
use Mirasvit\SeoFilter\Api\Service\FriendlyUrlServiceInterface;
use Mirasvit\SeoFilter\Api\Service\LnServiceInterface;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;

class AttributeFilterPlugin
{
    public function __construct(
        HtmlPager $htmlPagerBlock,
        CategoryRepositoryInterface $categoryRepository,
        FriendlyUrlServiceInterface $friendlyUrlService,
        UrlHelper $urlHelper,
        UrlInterface $url,
        Config $config,
        LnServiceInterface $lnService
    ) {
        $this->htmlPagerBlock     = $htmlPagerBlock;
        $this->categoryRepository = $categoryRepository;
        $this->friendlyUrlService = $friendlyUrlService;
        $this->urlHelper          = $urlHelper;
        $this->url                = $url;
        $this->config             = $config;
        $this->lnService          = $lnService;
        $this->storeId            = $urlHelper->getStoreId();
    }

    /**
     * Get filter item url
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @param string                                   $url
     * @return string
     */
    public function afterGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $url)
    {
        if (!$this->config->isEnabled($this->storeId)) {
            return $url;
        }

        if (!$this->config->isEnabled($this->storeId)
            || !$this->urlHelper->isCategoryPage()
            || $this->lnService->isLnEnabled()) {
            return $this->getOriginalUrl($item);
        }
        if ($item->getFilter()->getRequestVar() == 'cat') {
            $categoryUrl = $this->categoryRepository
                ->get($item->getValue(), $this->storeId)
                ->getUrl();

            return $this->urlHelper->addUrlParams($categoryUrl);
        }

        $filter = $item->getFilter();
        if (empty($filter)) {
            return $this->getOriginalUrl($item);
        }

        if (!$filter->getData('attribute_model')) {
            return $this->getOriginalUrl($item);
        }

        $attributeId   = $filter->getAttributeModel()->getAttributeId();
        $attributeCode = $filter->getAttributeModel()->getAttributeCode();
        $optionId      = $item->getValue();

        //fix default Magento bug (default Magento don't have multiselect)
        if ($attributeCode == PriceRewriteInterface::PRICE
            && (strpos($optionId, ',') !== false)) {
            $optionId = strtok($optionId, ',');
        }

        if (!$attributeId || !$attributeCode || !$optionId) {
            return $this->getOriginalUrl($item);
        }

        $url = $this->friendlyUrlService->getFriendlyUrl($attributeCode, $attributeId, $optionId);

        return $this->urlHelper->addUrlParams($url);
    }

    /**
     * Get url for remove item from filter
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return string
     */
    public function afterGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
        if (!$this->config->isEnabled($this->storeId)
            || !$this->urlHelper->isCategoryPage()
            || $this->lnService->isLnEnabled()) {
            return $this->getOriginalRemoveUrl($item);
        }

        $filter = $item->getFilter();

        if (empty($filter)) {
            return $this->getOriginalRemoveUrl($item);
        }

        if (!$filter->getData('attribute_model')) {
            return $this->getOriginalUrl($item);
        }

        $attributeId   = $filter->getAttributeModel()->getAttributeId();
        $attributeCode = $filter->getAttributeModel()->getAttributeCode();
        $optionId      = $item->getValue();
        if (!$attributeId || !$attributeCode || !$optionId) {
            return $this->getOriginalRemoveUrl($item);
        }

        $url = $this->friendlyUrlService->getFriendlyUrl($attributeCode, $attributeId, $optionId, true);

        return $this->urlHelper->addUrlParams($url);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @return string
     */
    protected function getOriginalUrl($item)
    {
        $query = [
            $item->getFilter()->getRequestVar()     => $item->getValue(),
            // exclude current page from urls
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
        $query                  = [$item->getFilter()->getRequestVar() => $item->getFilter()->getResetValue()];
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;

        return $this->url->getUrl('*/*/*', $params);
    }
}