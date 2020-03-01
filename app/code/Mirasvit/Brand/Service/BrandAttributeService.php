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

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Mirasvit\Brand\Api\Data\BrandInterface;
use Mirasvit\Brand\Api\Service\BrandAttributeServiceInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Magento\Store\Model\StoreManagerInterface;

class BrandAttributeService implements BrandAttributeServiceInterface
{
    /**
     * @var BrandPageInterface[]
     */
    private $brandPagesByOptions = [];
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;
    /**
     * @var BrandPageRepositoryInterface
     */
    private $brandPageRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ConfigInterface $config,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        BrandPageRepositoryInterface $brandPageRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->brandPageRepository = $brandPageRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandAttributeId()
    {
        $brandAttributeId = null;
        if ($brandAttributeCode = $this->config->getGeneralConfig()->getBrandAttribute()) {
            $brandAttributeId = 1;
        }

        return $brandAttributeId;
    }

    /**
     * Return all brands that can be visible.
     * Only configured (brand pages) if appropriate option enabled.
     *
     * @inheritdoc
     */
    public function getVisibleBrandOptions()
    {
        $visibleOptions = [];

        if ($this->config->getGeneralConfig()->getBrandAttribute()) {
            $isShowNotConfiguredBrands = $this->config->getGeneralConfig()->isShowNotConfiguredBrands();
            $brandPages = $this->getBrandPagesByOptions();
            $attribute = $this->getAttribute();

            foreach ($this->getBrandOptions() as $idx => $option) {
                $page = isset($brandPages[$option['value']]) ? $brandPages[$option['value']] : null;
                if ($isShowNotConfiguredBrands || $page) {
                    $option[BrandInterface::PAGE] = $page;
                    $option[BrandInterface::ATTRIBUTE_ID] = $attribute->getId();
                    $option[BrandInterface::ATTRIBUTE_CODE] = $attribute->getAttributeCode();

                    $visibleOptions[] = $option;
                }
            }
        }

        return $visibleOptions;
    }

    /**
     * @inheritdoc
     */
    private function getBrandOptions()
    {
        $options = $this->getAttribute()->getSource()->getAllOptions();
        foreach ($options as $idx => $option) {
            if (!$option['value'] || !$option['label']) {
                unset($options[$idx]);
            }
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    private function getBrandPagesByOptions()
    {
        if (!$this->brandPagesByOptions) {
            $brandPageCollection = $this->brandPageRepository->getCollection()
                ->addStoreFilter($this->storeManager->getStore())
                ->addFieldToFilter(BrandPageInterface::ATTRIBUTE_ID, $this->getAttribute()->getId())
                ->addFieldToFilter(BrandPageInterface::IS_ACTIVE, 1);

            /** @var BrandPageInterface $item */
            foreach ($brandPageCollection as $item) {
                $this->brandPagesByOptions[$item->getAttributeOptionId()] = $item;
            }
        }

        return $this->brandPagesByOptions;
    }

    /**
     * Get attribute used as the brand.
     *
     * @return AbstractAttribute
     */
    private function getAttribute()
    {
        return $this->productAttributeRepository->get($this->config->getGeneralConfig()->getBrandAttribute());
    }
}
