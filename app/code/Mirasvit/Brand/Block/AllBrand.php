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

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Mirasvit\Brand\Api\Data\BrandInterface;
use Mirasvit\Brand\Api\Repository\BrandRepositoryInterface;
use Mirasvit\Brand\Api\Service\BrandAttributeServiceInterface;
use Magento\Framework\Filter\FilterManager;
use Mirasvit\Brand\Api\Config\ConfigInterface;

class AllBrand extends Template
{
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;
    /**
     * @var BrandAttributeServiceInterface
     */
    private $brandAttributeService;
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(
        BrandRepositoryInterface $brandRepository,
        Context $context,
        BrandAttributeServiceInterface $brandAttributeService,
        ConfigInterface $config
    ) {
        $this->brandRepository = $brandRepository;
        $this->brandAttributeService = $brandAttributeService;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Return collection of brands grouped by first letter.
     *
     * @return array
     */
    public function getBrandsByLetters()
    {
        $collectionByLetters = [];
        $collection = $this->brandRepository->getCollection();

        foreach ($collection as $brand) {
            $letter = strtoupper(mb_substr($brand->getLabel(), 0, 1));

            if (isset($collectionByLetters[$letter])) {
                $collectionByLetters[$letter][$brand->getLabel()] = $brand;
            } else {
                $collectionByLetters[$letter] = [$brand->getLabel() => $brand];
            }
        }

        // sort brands alphabetically
        ksort($collectionByLetters);
        foreach ($collectionByLetters as $letter => $brands) {
            ksort($brands);
            $collectionByLetters[$letter] = $brands;
        }

        return $collectionByLetters;
    }

    /**
     * @param BrandInterface $brand
     *
     * @return bool
     */
    public function canShowImage(BrandInterface $brand)
    {
        return $this->config->getAllBrandPageConfig()->isShowBrandLogo() && $brand->getImage();
    }
}
