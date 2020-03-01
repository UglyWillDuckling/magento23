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



namespace Mirasvit\LayeredNavigation\Plugin;

use Magento\Framework\Registry;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface as Config;
use Magento\Framework\View\ConfigInterface;

class ImageProductRegisterPlugin
{
    /**
     * Media config node
     */
    const MEDIA_TYPE_CONFIG_NODE = 'images';

    /**
     * Use as destination subdir
     */
    const ATTRIBUTE_TYPE = 'type';

    /**
     * Product
     */
    const PRODUCT = 'product';

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry,
        ConfigInterface $viewConfig
    ) {
        $this->registry = $registry;
        $this->viewConfig = $viewConfig;
    }

    /**
     * @param \Magento\Catalog\Helper\Image $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeInit($subject, $product, $imageId, $attributes = [])
    {
        $attributes = $this->viewConfig->getViewConfig()
            ->getMediaAttributes('Magento_Catalog', self::MEDIA_TYPE_CONFIG_NODE, $imageId);
        $type = isset($attributes[self::ATTRIBUTE_TYPE]) ? $attributes[self::ATTRIBUTE_TYPE] : null;
        $productData = [
            self::PRODUCT => $product,
            self::ATTRIBUTE_TYPE => $type,
        ];
        $this->registry->unregister(Config::NAV_IMAGE_REG_PRODUCT_DATA);
        $this->registry->register(Config::NAV_IMAGE_REG_PRODUCT_DATA, $productData);
    }
}