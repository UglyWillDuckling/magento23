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
 * @package   mirasvit/module-ajax-scroll
 * @version   1.0.7
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Scroll\Plugin\Frontend\Magento\Framework\App\Action\AbstractAction;

use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Mirasvit\Scroll\Model\Config;

class ScrollResponsePlugin
{
    const PARAM_IS_SCROLL = 'is_scroll';

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * If request triggered by scroll module - return appropriate scroll response.
     *
     * @param AbstractAction $subject
     * @param \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http $response
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http
     */
    public function afterDispatch(AbstractAction $subject, $response)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $subject->getRequest();
        if ($this->canProcess($request, $response)) {
            /** @var \Magento\Framework\View\LayoutInterface $layout */
            $layout = $response->getLayout();
            /** @var \Mirasvit\Scroll\Block\Scroll $scroll */
            $scroll = $layout->getBlock('product.list.scroll');
            $products = $this->getProductListBlock($request, $layout);

            return $subject->getResponse()->representJson(\Zend_Json::encode([
                'products' => $products ? $products->toHtml() : '',
                'config' => $scroll ? $scroll->getJsConfig() : []
            ]));
        }

        return $response;
    }

    /**
     * @param RequestInterface|\Magento\Framework\App\Request\Http $request
     *
     * @param \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http $response
     *
     * @return bool
     */
    protected function canProcess(RequestInterface $request, $response)
    {
        return $this->config->isEnabled()
            && $request->isAjax()
            && $request->has(self::PARAM_IS_SCROLL)
            && $response instanceof Page;
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\View\LayoutInterface $layout
     *
     * @return \Magento\Catalog\Block\Product\ListProduct|bool
     */
    private function getProductListBlock($request, $layout)
    {
        if (in_array($request->getFullActionName(), ['brand_brand_view', 'all_products_page_index_index'], true)) {
            $products = $layout->getBlock('category.products.list');
        } else {
            $products = $layout->getBlock('category.products') ?: $layout->getBlock('search.result');
        }

        return $products;
    }
}
