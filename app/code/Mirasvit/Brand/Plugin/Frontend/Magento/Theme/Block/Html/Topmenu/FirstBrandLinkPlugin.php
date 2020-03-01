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



namespace Mirasvit\Brand\Plugin\Frontend\Magento\Theme\Block\Html\Topmenu;

use Magento\Framework\Data\Tree\Node;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Api\Service\BrandUrlServiceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Topmenu;
use Mirasvit\Brand\Model\Config\Source\BrandsLinkPositionOptions;

class FirstBrandLinkPlugin
{
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $url,
        ConfigInterface $config,
        BrandUrlServiceInterface $brandUrlService,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->config = $config;
        $this->brandUrlService = $brandUrlService;
        $this->storeManager = $storeManager;
    }

    public function beforeGetHtml(Topmenu $subject, ...$args)
    {
        if (!$this->isBrandLinkEnabled()) {
            return;
        }

        $node = new Node(
            $this->_getNodeAsArray(),
            'id',
            $subject->getMenu()->getTree(),
            $subject->getMenu()
        );
        $subject->getMenu()->addChild($node);
    }

    /**
     * @return array
     */
    protected function _getNodeAsArray()
    {
        $url = $this->brandUrlService->getBaseBrandUrl();

        return [
            'name' => $this->config->getGeneralConfig()->getBrandLinkLabel() ?: __('Brands'),
            'id' => 'm___all_brands_page_link',
            'url' => $url,
            'has_active' => false,
            'is_active' => $url === $this->url->getCurrentUrl()
        ];
    }

    /**
     * @return int
     */
    protected function getBrandLinkPosition()
    {
        return BrandsLinkPositionOptions::TOP_MENU_FIRST;
    }

    /**
     * @return bool
     */
    protected function isBrandLinkEnabled()
    {
        return $this->getBrandLinkPosition() == $this->config->getGeneralConfig()->getBrandLinkPosition();
    }
}
