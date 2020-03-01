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


namespace Mirasvit\Brand\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Brand\Api\Service\BrandLogoServiceInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Block\Logo;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http as RequestHttp;
use Mirasvit\Brand\Model\Config\Source\ProductPageBrandLogoDescription;

class LogoProductAdapter extends Logo
{
    /**
     * @var string
     */
    private $brandAttribute;
    /**
     * @var string
     */
    private $productPageBrandLogoDescription;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        BrandLogoServiceInterface $brandLogoService,
        ConfigInterface $config,
        RequestHttp $request,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $brandLogoService, $config, $request, $data);
        $this->registry = $registry;
        $this->brandAttribute = $config->getGeneralConfig()->getBrandAttribute();
        $this->productPageBrandLogoDescription = $config->getBrandLogoConfig()->getProductPageBrandLogoDescription();
        $this->setBrandDataForLogo();
    }

    /**
     * @return void
     */
    private function setBrandDataForLogo()
    {
        if ($product = $this->registry->registry('current_product')) {
            $optionId = $product->getData($this->brandAttribute);
            $this->brandLogoService->setBrandDataByOptionId($optionId);    
        }
    }

    /**
     * @return string
     */
    public function getImageWidth()
    {
        return $this->config->getBrandLogoConfig()->getProductPageBrandLogoImageWidth();
    }


    /**
     * @return string
     */
    public function getTooltipContent()
    {
        return $this->brandLogoService->getLogoTooltipContent(
            $this->config->getBrandLogoConfig()->getProductPageBrandLogoTooltip()
        );
    }

    /**
     * @return string
     */
    public function getBrandDescription()
    {
        $description = '';
        if ($this->productPageBrandLogoDescription
            == ProductPageBrandLogoDescription::BRAND_LOGO_DESCRIPTION) {
            $description = $this->brandLogoService->getBrandDescription();
        } elseif ($this->productPageBrandLogoDescription
            == ProductPageBrandLogoDescription::BRAND_LOGO_SHORT_DESCRIPTION) {
            $description = $this->brandLogoService->getBrandShortDescription();
        }

        return $description;
    }

    /**
     * {@inheritdoc}
     */
    public function _toHtml()
    {
        if ($this->isProductPage()
            && ($product = $this->registry->registry('current_product'))
            && !$product->getData($this->brandAttribute)) {
                return '';
        }

        return parent::_toHtml();
    }


}
