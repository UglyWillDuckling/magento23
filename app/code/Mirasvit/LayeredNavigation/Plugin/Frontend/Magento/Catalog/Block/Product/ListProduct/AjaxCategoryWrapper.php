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



namespace Mirasvit\LayeredNavigation\Plugin\Frontend\Magento\Catalog\Block\Product\ListProduct;

use Magento\Framework\App\Request\Http;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;

class AjaxCategoryWrapper
{
    use ConfigTrait;

    /**
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml($subject, $result)
    {
        if (!$this->isAjaxEnabled() && $subject->getNameInLayout() === 'category.products.list') {
            // use for filter opener
            return ConfigInterface::NAV_REPLACER_TAG . $result;
        }

        if (!$this->isAjaxEnabled()
            || $subject->getNameInLayout() !== 'category.products.list'
            || $this->isExternalRequest($this->request)
        ) {
            return $result;
        }

        return ConfigInterface::NAV_REPLACER_TAG . '<div id="' . ConfigInterface::AJAX_PRODUCT_LIST_WRAPPER_ID . '">'
            . $result . '</div>';
    }
}
