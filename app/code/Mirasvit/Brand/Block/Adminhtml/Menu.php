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



namespace Mirasvit\Brand\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Api\Service\BrandAttributeServiceInterface;

class Menu extends AbstractMenu
{
    /**
     * Menu constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        BrandAttributeServiceInterface $brandAttributeService
    ) {
        $this->visibleAt(['brand']);
        $this->config = $config;
        $this->brandAttributeService = $brandAttributeService;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_Brand::brand_brand',
            'title'    => __('Brand Pages'),
            'url'      => $this->urlBuilder->getUrl('brand/brand/index'),
        ]);

        if ($this->config->getGeneralConfig()->getBrandAttribute()) {
            $this->addItem([
                'resource' => 'Mirasvit_Brand::attribute',
                'title'    => __('Brand Attribute'),
                'url'      => $this->urlBuilder->getUrl('catalog/product_attribute/edit/', [
                    'attribute_id' => $this->brandAttributeService->getBrandAttributeId()
                ]),
            ]);
        }


        $this->addItem([
            'resource' => 'Mirasvit_Brand::brand_settings',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/brand'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_Brand::brand_get_support',
            'title'    => __('Get Support'),
            'url'      => 'https://mirasvit.com/support/',
        ]);

        return $this;
    }
}
