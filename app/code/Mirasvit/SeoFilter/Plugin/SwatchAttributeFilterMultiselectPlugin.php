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

use Magento\Eav\Model\ResourceModel\Entity\Attribute as EntityAttribute;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Mirasvit\SeoFilter\Api\Service\LnServiceInterface;
use Mirasvit\SeoFilter\Api\Service\RewriteServiceInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterUrlServiceInterface;
use Magento\Framework\ObjectManagerInterface;

class SwatchAttributeFilterMultiselectPlugin
{
    /**
     * SwatchAttributeFilterMultiselectPlugin constructor.
     * @param EntityAttribute $eavAttribute
     * @param UrlHelper $urlHelper
     * @param Config $config
     * @param LnServiceInterface $lnService
     * @param RewriteServiceInterface $rewrite
     * @param SeoFilterUrlServiceInterface $seoFilterUrlService
     */
    public function __construct(
        EntityAttribute $eavAttribute,
        UrlHelper $urlHelper,
        Config $config,
        LnServiceInterface $lnService,
        RewriteServiceInterface $rewrite,
        ObjectManagerInterface $objectManager
    ) {
        $this->eavAttribute = $eavAttribute;
        $this->urlHelper = $urlHelper;
        $this->config = $config;
        $this->lnService = $lnService;
        $this->storeId = $urlHelper->getStoreId();
        $this->rewrite = $rewrite;
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject
     * @param \Closure $proceed
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuildUrl($subject, $proceed, $attributeCode, $optionId)
    {
        if (!$this->config->isEnabled($this->storeId)
            || !$this->urlHelper->isCategoryPage()
            || !$this->lnService->isLnEnabled()) {
                return $proceed($attributeCode, $optionId);
        }

        $filterUrlArray = [];
        $activeFilters = $this->rewrite->getActiveFilters();
        if ((isset($activeFilters[$attributeCode]) && $activeFilters[$attributeCode])
            || (isset($activeFilters[$attributeCode])
                && strpos($activeFilters[$attributeCode], RewriteInterface::FILTER_SEPARATOR) !== false
                && in_array($attributeCode,
                    explode(RewriteInterface::FILTER_SEPARATOR, $activeFilters[$attributeCode])))
        ) {
            $attributeId = $this->eavAttribute->getIdByCode('catalog_product', $attributeCode);

            $filterUrlArray[$attributeCode] = $this->rewrite->getRewriteForFilterOption($attributeCode,
                $attributeId,
                $optionId
            );
            foreach ($filterUrlArray as $key => $value) {
                if (isset($activeFilters[$key])
                    && $attributeCode == $key
                    && (($activeFilters[$attributeCode] == $value)
                        || in_array($value,
                            explode(RewriteInterface::FILTER_SEPARATOR, $activeFilters[$attributeCode])))
                ) {
                        return $this->objectManager->get(SeoFilterUrlServiceInterface::class)
                            ->getRemoveMultiselectFriendlyUrl(
                                $attributeCode,
                                $attributeId,
                                $optionId,
                                true
                        );
                        break;
                }
            }
        }

        return $proceed($attributeCode, $optionId);
    }
}