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


namespace Mirasvit\Brand\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Api\Service\BrandAttributeServiceInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Service\ImageUrlServiceInterface;
use Mirasvit\Brand\Api\Service\BrandUrlServiceInterface;
use Mirasvit\Brand\Model\Config\Source\BrandSliderOrder;

class BrandSlider extends Template implements BlockInterface
{
    protected $sliderData;

    public function __construct(
        Context $context,
        BrandPageRepositoryInterface $brandPageRepository,
        ConfigInterface $config,
        BrandAttributeServiceInterface $brandAttributeService,
        ImageUrlServiceInterface $imageUrlService,
        BrandUrlServiceInterface $brandUrlService,
        array $data = []
    ) {
        $this->brandPageRepository = $brandPageRepository;
        $this->sliderConfig = $config->getBrandSliderConfig();
        $this->brandAttributeService = $brandAttributeService;
        $this->storeId = $context->getStoreManager()->getStore()->getStoreId();
        $this->imageUrlService = $imageUrlService;
        $this->brandUrlService = $brandUrlService;
        parent::__construct($context, $data);
        $this->getSliderOptions();
    }


    /**
     * @return array|null
     */
    public function getSliderItems()
    {
        $attributeId = $this->brandAttributeService->getBrandAttributeId();
        $сollection = $this->brandPageRepository->getCollection()
            ->addStoreFilter($this->storeId)
            ->addFieldToFilter(BrandPageInterface::ATTRIBUTE_ID, $attributeId)
            ->addFieldToFilter(BrandPageInterface::IS_SHOW_IN_BRAND_SLIDER, 1);


        if ($this->getOrder() == BrandSliderOrder::SLIDER_POSITION_ORDER) {
            $сollection->setOrder(
                BrandPageInterface::SLIDER_POSITION,
                'asc'
            );
        } else {
            $сollection->setOrder(
                BrandPageInterface::BRAND_TITLE,
                'asc'
            );
        }

        return $сollection->getData();
    }

    /**
     * @return string
     */
    public function getImageUrl($imageName)
    {
        return $this->imageUrlService->getImageUrl($imageName);
    }

    /**
     * @return string
     */
    public function getBrandUrl($urlKey, $brandTitle)
    {
        return $this->_storeManager->getStore()->getBaseUrl()
            . $this->brandUrlService->getBrandUrl($urlKey, $brandTitle);
    }

    /**
     * @return array
     */
    public function getSliderOptions()
    {
        if ($this->sliderData === null) {
            $this->sliderData = $this->getData();
        }

        return $this->sliderData;
    }

    /**
     * @return int
     */
    public function getItemsLimit()
    {
        return (isset($this->sliderData['ItemsLimit']))
            ? $this->sliderData['ItemsLimit']
            : $this->sliderConfig->getItemsLimit();
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return (isset($this->sliderData['Order']))
            ? $this->sliderData['Order']
            : $this->sliderConfig->getOrder();
    }

    /**
     * @return bool
     */
    public function isShowTitle()
    {
        return (isset($this->sliderData['isShowTitle']))
            ? $this->sliderData['isShowTitle']
            : $this->sliderConfig->isShowTitle();
    }

    /**
     * @return string
     */
    public function getTitleText()
    {
        return (isset($this->sliderData['TitleText']))
            ? $this->sliderData['TitleText']
            : $this->sliderConfig->getTitleText();
    }

    /**
     * @return string
     */
    public function getTitleTextColor()
    {
        return (isset($this->sliderData['TitleTextColor']))
            ? $this->sliderData['TitleTextColor']
            : $this->sliderConfig->getTitleTextColor();
    }

    /**
     * @return string
     */
    public function getTitleBackgroundColor()
    {
        return (isset($this->sliderData['TitleBackgroundColor']))
            ? $this->sliderData['TitleBackgroundColor']
            : $this->sliderConfig->getTitleBackgroundColor();
    }

    /**
     * @return bool
     */
    public function isShowBrandLabel()
    {
        return (isset($this->sliderData['isShowBrandLabel']))
            ? $this->sliderData['isShowBrandLabel']
            : $this->sliderConfig->isShowBrandLabel();
    }

    /**
     * @return string
     */
    public function getBrandLabelColor()
    {
        return (isset($this->sliderData['BrandLabelColor']))
            ? $this->sliderData['BrandLabelColor']
            : $this->sliderConfig->getBrandLabelColor();
    }

    /**
     * @return bool
     */
    public function isShowButton()
    {
        return (isset($this->sliderData['isShowButton']))
            ? $this->sliderData['isShowButton']
            : $this->sliderConfig->isShowButton();
    }

    /**
     * @return bool
     */
    public function isShowPagination()
    {
        return (isset($this->sliderData['isShowPagination']))
            ? $this->sliderData['isShowPagination']
            : $this->sliderConfig->isShowPagination();
    }

    /**
     * @return bool
     */
    public function isAutoPlay()
    {
        return (isset($this->sliderData['isAutoPlay']))
            ? $this->sliderData['isAutoPlay']
            : $this->sliderConfig->isAutoPlay();
    }

    /**
     * @return bool
     */
    public function isAutoPlayLoop()
    {
        return (isset($this->sliderData['isAutoPlayLoop']))
            ? $this->sliderData['isAutoPlayLoop']
            : $this->sliderConfig->isAutoPlayLoop();
    }

    /**
     * @return int
     */
    public function getAutoPlayInterval()
    {
        return (isset($this->sliderData['AutoPlayInterval']))
            ? $this->sliderData['AutoPlayInterval']
            : $this->sliderConfig->getAutoPlayInterval();
    }

    /**
     * @return int
     */
    public function getPauseOnHover()
    {
        return (isset($this->sliderData['PauseOnHover']))
            ? $this->sliderData['PauseOnHover']
            : $this->sliderConfig->getPauseOnHover();
    }

    /**
     * @return int
     */
    public function getSliderWidth()
    {
        return (isset($this->sliderData['SliderWidth']))
            ? $this->sliderData['SliderWidth']
            : $this->sliderConfig->getSliderWidth();
    }

    /**
     * @return int
     */
    public function getSliderImageWidth()
    {
        return (isset($this->sliderData['SliderImageWidth']))
            ? $this->sliderData['SliderImageWidth']
            : $this->sliderConfig->getSliderImageWidth();
    }

    /**
     * @return int
     */
    public function getSpacingBetweenImages()
    {
        return (isset($this->sliderData['SpacingBetweenImages']))
            ? $this->sliderData['SpacingBetweenImages']
            : $this->sliderConfig->getSpacingBetweenImages();
    }

    /**
     * @return string
     */
    public function getInactivePagingColor()
    {
        return (isset($this->sliderData['InactivePagingColor']))
            ? $this->sliderData['InactivePagingColor']
            : $this->sliderConfig->getInactivePagingColor();
    }

    /**
     * @return string
     */
    public function getActivePagingColor()
    {
        return (isset($this->sliderData['ActivePagingColor']))
            ? $this->sliderData['ActivePagingColor']
            : $this->sliderConfig->getActivePagingColor();
    }

    /**
     * @return string
     */
    public function getHoverPagingColor()
    {
        return (isset($this->sliderData['HoverPagingColor']))
            ? $this->sliderData['HoverPagingColor']
            : $this->sliderConfig->getHoverPagingColor();
    }

    /**
     * @return string
     */
    public function getNavigationButtonsColor()
    {
        return (isset($this->sliderData['NavigationButtonsColor']))
            ? $this->sliderData['NavigationButtonsColor']
            : $this->sliderConfig->getNavigationButtonsColor();
    }
}
