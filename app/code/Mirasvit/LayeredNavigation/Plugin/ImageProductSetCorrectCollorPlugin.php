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
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Magento\Framework\App\Request\Http as RequestHttp;
use Mirasvit\LayeredNavigation\Plugin\ImageProductRegisterPlugin;
use Mirasvit\LayeredNavigation\Api\Service\VersionServiceInterface;

class ImageProductSetCorrectCollorPlugin
{
    /**
     * @param Registry $registry
     * @param RequestHttp $request
     */
    public function __construct(
        Registry $registry,
        RequestHttp $request,
        VersionServiceInterface $versionService
    ) {
        $this->registry = $registry;
        $this->request = $request;
        $this->versionService = $versionService;
    }

    /**
     * @param string $file
     * @return string
     */
    public function beforeSetBaseFile($subject, $file)
    {
        $productData = $this->registry->registry(ConfigInterface::NAV_IMAGE_REG_PRODUCT_DATA);
        if (!$productData || !is_object($productData[ImageProductRegisterPlugin::PRODUCT])) {
            return $file;
        }

        $product = $productData[ImageProductRegisterPlugin::PRODUCT];
        $destinationSubdir = $productData[ImageProductRegisterPlugin::ATTRIBUTE_TYPE];

        if (($colorAttribute = $this->request->getParam('color'))
            && $product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);

            foreach ($children as $child) {
                if ($child->getData('color') == $colorAttribute
                    && ($imageFile = $child->getData($destinationSubdir))) {
                    $file = $imageFile;
                    break;
                }
            }
        }

        return (version_compare($this->versionService->getVersion(), '2.2.0') < 0) ? [$file] : $file;
    }
}