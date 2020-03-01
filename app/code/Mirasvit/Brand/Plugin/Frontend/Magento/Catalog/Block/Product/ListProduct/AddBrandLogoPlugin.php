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



namespace Mirasvit\Brand\Plugin\Frontend\Magento\Catalog\Block\Product\ListProduct;

use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Api\Service\BrandLogoServiceInterface;
use Mirasvit\Brand\Api\Service\BrandAttributeServiceInterface;
use Magento\Catalog\Block\Product\ListProduct;

class AddBrandLogoPlugin
{
    public function __construct(
        ConfigInterface $config,
        BrandLogoServiceInterface $brandLogoService,
        BrandAttributeServiceInterface $brandAttributeService
    ) {
        $this->isProductListProduct = $config->getBrandLogoConfig()->isProductListBrandLogoEnabled();
        $this->brandLogoService = $brandLogoService;
        $this->brandAttribute = $config->getGeneralConfig()->getBrandAttribute();
    }

    /**
     * @param ListProduct                    $subject
     * @param callable                       $proceed
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function aroundGetProductDetailsHtml(
        ListProduct $subject,
        callable $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $html = $proceed($product);

        if (!is_object($product)
            || !$this->isProductListProduct
            || !$this->brandAttribute
        ) {
            return $html;
        }

        $product->load($product->getId()); // in some cases attribute's data is absent if the model is not loaded
        $optionId = $product->getData($this->brandAttribute);
        if (!$optionId) {
            return $html;
        }

        $this->brandLogoService->setBrandDataByOptionId($optionId);
        $logo = $this->brandLogoService->getLogoHtml();

        return $html . $logo;
    }
}
