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
use Magento\Framework\App\Request\Http as RequestHttp;

class Logo extends Template
{
    /**
     * @var RequestHttp
     */
    private $request;
    /**
     * @var ConfigInterface
     */
    protected $config;
    /**
     * @var BrandLogoServiceInterface
     */
    protected $brandLogoService;

    public function __construct(
        Context $context,
        BrandLogoServiceInterface $brandLogoService,
        ConfigInterface $config,
        RequestHttp $request,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request = $request;
        $this->config = $config;
        $this->brandLogoService = $brandLogoService;
    }

    /**
     * @return string
     */
    public function getLogoImageUrl()
    {
        return $this->brandLogoService->getLogoImageUrl();
    }

    /**
     * @return string
     */
    public function getImageWidth()
    {
        return $this->config->getBrandLogoConfig()->getProductListBrandLogoImageWidth();
    }

    /**
     * @return string
     */
    public function getBrandTitle()
    {
        return $this->brandLogoService->getBrandTitle();
    }

    /**
     * @return string
     */
    public function getBrandUrl()
    {
        return $this->brandLogoService->getBrandUrl();
    }

    /**
     * @return string
     */
    public function getTooltipContent()
    {
        return $this->brandLogoService->getLogoTooltipContent(
            $this->config->getBrandLogoConfig()->getProductListBrandLogoTooltip()
        );
    }

    /**
     * @return string
     */
    public function isProductPage()
    {
        if ($this->request->getFullActionName() === 'catalog_product_view') {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function _toHtml()
    {
        if (($this->isProductPage() && !$this->config->getBrandLogoConfig()->isProductPageBrandLogoEnabled())
            || (!$this->isProductPage() && !$this->config->getBrandLogoConfig()->isProductListBrandLogoEnabled())) {
            return '';
        }

        return parent::_toHtml();
    }
}
