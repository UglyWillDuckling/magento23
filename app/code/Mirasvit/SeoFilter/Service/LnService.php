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

use Mirasvit\SeoFilter\Api\Service\LnServiceInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class LnService implements LnServiceInterface
{
    public function __construct(
        Manager $moduleManager,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
        $this->storeId = $storeManager->getStore()->getStoreId();
    }

    /**
     * {@inheritdoc}
     */
    public function isLnEnabled()
    {
        return $this->moduleManager->isEnabled('Mirasvit_LayeredNavigation');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSliderOptions()
    {
        $sliderConfig = $this->objectManager
            ->get(\Mirasvit\LayeredNavigation\Api\Config\SliderConfigInterface::class);
        if ($sliderOptions = $sliderConfig->getSliderOptions($this->storeId)) {
            return $sliderOptions;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getLnSliderOptions()
    {
        if ($this->isLnEnabled()
            && $sliderOptions = $this->getSliderOptions()) {
                return $sliderOptions;
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalFiltersConfig()
    {
        return $this->objectManager
            ->get(\Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function isLnNewFilterEnabled()
    {
        if ($this->isLnEnabled()
            && $this->getAdditionalFiltersConfig()->isNewFilterEnabled($this->storeId)) {
                return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLnOnSaleFilterEnabled()
    {
        if ($this->isLnEnabled()
            && $this->getAdditionalFiltersConfig()->isOnSaleFilterEnabled($this->storeId)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLnStockFilterEnabled()
    {
        if ($this->isLnEnabled()
            && $this->getAdditionalFiltersConfig()->isStockFilterEnabled($this->storeId)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLnRatingFilterEnabled()
    {
        if ($this->isLnEnabled()
            && $this->getAdditionalFiltersConfig()->isRatingFilterEnabled($this->storeId)) {
            return true;
        }

        return false;
    }
}