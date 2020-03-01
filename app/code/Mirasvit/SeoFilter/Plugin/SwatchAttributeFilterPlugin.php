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



namespace Mirasvit\SeoFilter\Plugin;

use Mirasvit\SeoFilter\Api\Service\FriendlyUrlServiceInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EntityAttribute;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;
use Magento\Framework\UrlInterface;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Mirasvit\SeoFilter\Api\Service\LnServiceInterface;

class SwatchAttributeFilterPlugin
{
    /**
     * @param FriendlyUrlServiceInterface $friendlyUrlService
     * @param EntityAttribute $eavAttribute
     * @param UrlHelper $urlHelper
     * @param UrlInterface $urlBuilder
     * @param Config $config
     */
    public function __construct(
        FriendlyUrlServiceInterface $friendlyUrlService,
        EntityAttribute $eavAttribute,
        UrlHelper $urlHelper,
        UrlInterface $urlBuilder,
        Config $config,
        LnServiceInterface $lnService
    ) {
        $this->friendlyUrlService = $friendlyUrlService;
        $this->eavAttribute = $eavAttribute;
        $this->urlHelper = $urlHelper;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
        $this->lnService = $lnService;
        $this->storeId = $urlHelper->getStoreId();
    }

    /**
     * @param Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject
     * @param \Closure $proceed
     * @param string $attributeCode
     * @param int $optionId
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuildUrl($subject, $proceed, $attributeCode, $optionId)
    {
        if (!$this->config->isEnabled($this->storeId)
            || !$this->urlHelper->isCategoryPage()
            || $this->lnService->isLnEnabled()) {
                return $this->getOriginalUrl($attributeCode, $optionId);
        }
        $attributeId = $this->eavAttribute->getIdByCode('catalog_product', $attributeCode);
        if ($attributeId) {
            $url = $this->friendlyUrlService->getFriendlyUrl($attributeCode, $attributeId, $optionId);
        } else {
            $url = $this->getOriginalUrl($attributeCode, $optionId);
        }

        return $this->urlHelper->addUrlParams($url);
    }

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     */
    protected function getOriginalUrl($attributeCode, $optionId) {
        $query = [$attributeCode => $optionId];

        return $this->urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}