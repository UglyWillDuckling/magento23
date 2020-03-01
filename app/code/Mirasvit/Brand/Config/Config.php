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



namespace Mirasvit\Brand\Config;

use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Api\Config\GeneralConfigInterface;
use Mirasvit\Brand\Api\Config\BrandPageConfigInterface;
use Mirasvit\Brand\Api\Config\AllBrandPageConfigInterface;
use Mirasvit\Brand\Api\Config\BrandSliderConfigInterface;
use Mirasvit\Brand\Api\Config\MoreFromBrandConfigInterface;
use Mirasvit\Brand\Api\Config\BrandLogoConfigInterface;

class Config implements ConfigInterface
{
    public function __construct(
        GeneralConfigInterface $generalConfig,
        BrandPageConfigInterface $brandPageConfig,
        AllBrandPageConfigInterface $allBrandPageConfig,
        BrandSliderConfigInterface $brandSliderConfig,
        MoreFromBrandConfigInterface $moreFromBrandConfig,
        BrandLogoConfigInterface $brandLogoConfig
    ) {
        $this->generalConfig = $generalConfig;
        $this->brandPageConfig = $brandPageConfig;
        $this->allBrandPageConfig = $allBrandPageConfig;
        $this->brandSliderConfig = $brandSliderConfig;
        $this->moreFromBrandConfig = $moreFromBrandConfig;
        $this->brandLogoConfig = $brandLogoConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneralConfig()
    {
        return $this->generalConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandPageConfig()
    {
        return $this->brandPageConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllBrandPageConfig()
    {
        return $this->allBrandPageConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandSliderConfig()
    {
        return $this->brandSliderConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getMoreFromBrandConfig()
    {
        return $this->moreFromBrandConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandLogoConfig()
    {
        return $this->brandLogoConfig;
    }
}
