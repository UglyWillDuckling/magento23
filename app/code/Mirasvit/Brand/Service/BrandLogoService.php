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



namespace Mirasvit\Brand\Service;

use Magento\Framework\App\Request\Http;
use Mirasvit\Brand\Api\Service\BrandLogoServiceInterface;
use Magento\Framework\View\Element\BlockFactory;
use Mirasvit\Brand\Block\Logo;
use Mirasvit\Brand\Api\Service\ImageUrlServiceInterface;
use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Service\BrandUrlServiceInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;

class BrandLogoService implements BrandLogoServiceInterface
{
    protected static $brandDataPrepared;

    protected $logo;

    protected $title;

    protected $urlKey;

    protected $description;

    protected $shortDescription;

    public function __construct(
        BlockFactory $blockFactory,
        ImageUrlServiceInterface $imageUrlService,
        BrandPageRepositoryInterface $brandPageRepository,
        BrandUrlServiceInterface $brandUrlService,
        ConfigInterface $config
    ) {
        $this->blockFactory = $blockFactory;
        $this->imageUrlService = $imageUrlService;
        $this->brandPageRepository = $brandPageRepository;
        $this->brandUrlService = $brandUrlService;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLogoHtml()
    {
        return $this->blockFactory
            ->createBlock(Logo::class)
            ->setTemplate('Mirasvit_Brand::logo/logo.phtml')
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getLogoImageUrl($image = false)
    {
        $image = ($image) ? false : 'thumbnail';
        return $this->imageUrlService->getImageUrl($this->logo, $image);
    }

    /**
     * @return string
     */
    public function getBrandTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getBrandUrl()
    {
        return $this->brandUrlService->getBrandUrl($this->urlKey, $this->getBrandTitle());
    }

    /**
     * @return string
     */
    public function getBrandDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getBrandShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param array $tooltip
     * @return string
     */
    public function getLogoTooltipContent($tooltip)
    {
        $tooltipContent = '';
        $style = '';
        if ($tooltip) {
            if ($tooltipMaxImageWidth = $this->config->getBrandLogoConfig()->getTooltipMaxImageWidth()) {
                $style = 'style="max-width: ' . $tooltipMaxImageWidth . 'px !important;"';
            }
            $search = [
                BrandLogoServiceInterface::BRAND_TITLE_PATTERN,
                BrandLogoServiceInterface::BRAND_IMAGE_PATTERN,
                BrandLogoServiceInterface::BRAND_SMALL_IMAGE_PATTERN,
                BrandLogoServiceInterface::BRAND_DESCRIPTION_PATTERN,
                BrandLogoServiceInterface::BRAND_SHORT_DESCRIPTION_PATTERN
            ];
            $replace = [
                $this->getBrandTitle(),
                '<img ' . $style . 'src="' . $this->getLogoImageUrl(true) . '">',
                '<img src="' . $this->getLogoImageUrl() . '">',
                $this->getPreparedText($this->getBrandDescription()),
                $this->getPreparedText($this->getBrandShortDescription())
            ];

            $tooltipContent .= str_replace($search, $replace, $tooltip);
        }

        return $tooltipContent;
    }

    private function getPreparedText($text)
    {
        return str_replace(['"', "'"], ['&quot;', '&apos;'], $text);
    }

    /**
     * @param int $optionId
     * @return void
     */
    public function setBrandDataByOptionId($optionId)
    {
        $this->setBrandData();

        if (self::$brandDataPrepared
            && isset(self::$brandDataPrepared[$optionId])
            && self::$brandDataPrepared[$optionId]) {
                $this->logo = self::$brandDataPrepared[$optionId][BrandPageInterface::LOGO];
                $this->title = self::$brandDataPrepared[$optionId][BrandPageInterface::BRAND_TITLE];
                $this->urlKey = self::$brandDataPrepared[$optionId][BrandPageInterface::URL_KEY];
                $this->description = self::$brandDataPrepared[$optionId][BrandPageInterface::BRAND_DESCRIPTION];
                $this->shortDescription = self::$brandDataPrepared[$optionId][BrandPageInterface::BRAND_SHORT_DESCRIPTION];
        } else {
            $this->logo = false;
            $this->title = false;
            $this->urlKey = false;
            $this->description = false;
            $this->shortDescription = false;
        }
    }

    /**
     * @return void
     */
    private function setBrandData()
    {
        if (self::$brandDataPrepared === null) {
            self::$brandDataPrepared = [];
            if ($brandData = $this->brandPageRepository->getCollection()->getData()) {
                foreach ($brandData as $key => $brand) {
                    self::$brandDataPrepared[$brand[BrandPageInterface::ATTRIBUTE_OPTION_ID]] = $brand;
                }
            }
        }
    }
}