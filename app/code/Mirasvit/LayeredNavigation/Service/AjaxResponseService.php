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



namespace Mirasvit\LayeredNavigation\Service;

use Mirasvit\LayeredNavigation\Api\Service\AjaxResponseServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\UrlInterface;
use Mirasvit\LayeredNavigation\Api\Service\UrlServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\SeoFilterServiceInterface;
use Mirasvit\LayeredNavigation\Observer\FilterOpener;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFiltersConfigInterface;
use Magento\Framework\Module\Manager as ModuleManager;

class AjaxResponseService implements AjaxResponseServiceInterface
{
    private $resultRawFactory;

    private $urlBuilder;

    private $urlService;

    private $seoFilterService;

    private $filterOpener;

    private $config;

    private $storeId;

    private $request;

    private $horizontalFiltersConfig;

    private $moduleManager;

    public function __construct(
        RawFactory $resultRawFactory,
        UrlInterface $urlBuilder,
        UrlServiceInterface $urlService,
        SeoFilterServiceInterface $seoFilterService,
        ConfigInterface $config,
        FilterOpener $filterOpener,
        StoreManagerInterface $storeManager,
        Http $request,
        HorizontalFiltersConfigInterface $horizontalFiltersConfig,
        ModuleManager $moduleManager
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->urlBuilder = $urlBuilder;
        $this->urlService = $urlService;
        $this->seoFilterService = $seoFilterService;
        $this->filterOpener = $filterOpener;
        $this->config = $config;
        $this->storeId = $storeManager->getStore()->getStoreId();
        $this->request = $request;
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAjaxResponse($page)
    {
        $layout = $page->getLayout();

        if ($this->request->getFullActionName() === 'brand_brand_view'
            || $this->request->getFullActionName() === 'all_products_page_index_index') {
            $products = $layout->getBlock('category.products.list');
        } else {
            $ajaxscroll = ($this->moduleManager->isEnabled('Lof_AjaxScroll')) ? 'true' : '';
            if ($ajaxscroll && 0) {
                $data = [
                    'ajaxscroll' => $ajaxscroll,
                    'url'        => ($this->seoFilterService->isUseSeoFilter())
                        ? 'mNavigationAjax->getAjaxResult' : $this->getCurrentUrl(),
                ];
                $data = $this->prepareAjaxData($data);
                $response = $this->createResponse($data);

                return $response;
            }
            $products = ($layout->getBlock('category.products')) ?: $layout->getBlock('search.result');
        }

        $leftnav = ($layout->getBlock('catalog.leftnav')) ?: $layout->getBlock('catalogsearch.leftnav');
        $breadcrumbs = $layout->getBlock('breadcrumbs');
        $pageTitle = $layout->getBlock('page.main.title');

        $categoryViewData = '';
        $children = $layout->getChildNames('category.view.container');
        foreach ($children as $child) {
            $categoryViewData .= $layout->renderElement($child);
        }
        $categoryViewData = '<div class="category-view">' . $categoryViewData . '</div>';
        $horizontalNavigation = ($this->horizontalFiltersConfig
            ->isUseCatalogLeftnavHorisontalNavigation($this->storeId)
        ) ? $layout->getBlock('catalog.leftnav')->toHtml()
            . $layout->getBlock('m.catalog.navigation.horizontal.state')->toHtml()
            : $layout->getBlock('m.catalog.horizontal')->toHtml();

        $data = [
            'products'             => $products ? $products->toHtml() : '',
            'leftnav'              => $leftnav ? $this->prepareLeftnav($leftnav->toHtml()) : '',
            'breadcrumbs'          => $breadcrumbs ? $breadcrumbs->toHtml() : '',
            'pageTitle'            => $pageTitle ? $pageTitle->toHtml() : '',
            'categoryViewData'     => $categoryViewData,
            'url'                  => ($this->seoFilterService->isUseSeoFilter())
                ? 'mNavigationAjax->getAjaxResult' : $this->getCurrentUrl(),
            'horizontalNavigation' => '<div class="navigation-horizontal">'
                . $horizontalNavigation . '</div>',
        ];

        try {
            $sidebarTag = $layout->getElementProperty('div.sidebar.additional', Element::CONTAINER_OPT_HTML_TAG);
            $sidebarClass = $layout->getElementProperty('div.sidebar.additional', Element::CONTAINER_OPT_HTML_CLASS);
            $sidebarAdditional = $layout->renderNonCachedElement('div.sidebar.additional');
            $data['sidebar_additional'] = $sidebarAdditional;
            $data['sidebar_additional_selector'] = $sidebarTag . '.' . str_replace(' ', '.', $sidebarClass);
        } catch (\Exception $e) {
        }

        if ($this->moduleManager->isEnabled('Lof_AjaxScroll')) {
            $data['products'] .= $layout->createBlock('Lof\AjaxScroll\Block\Init')
                ->setTemplate('Lof_AjaxScroll::init.phtml')->toHtml();
            $data['products'] .= $layout->createBlock('Lof\AjaxScroll\Block\Init')
                ->setTemplate('Lof_AjaxScroll::scripts.phtml')->toHtml();
            $data['products'] .= "<script>window.ias.nextUrl = window.ias.getNextUrl();</script>";
        }

        if ($this->moduleManager->isEnabled('Mirasvit_Scroll')) {
            $data['products'] .= $layout->createBlock('Mirasvit\Scroll\Block\Scroll')
                ->setTemplate('Mirasvit_Scroll::scroll.phtml')->toHtml();
        }

        $data = $this->prepareAjaxData($data);
        $response = $this->createResponse($data);

        return $response;
    }


    protected function prepareLeftnav($html)
    {
        if ($this->filterOpener->isAllowedRouteName()
            && $this->filterOpener->isShowOpenedFilters()) {
            return $this->filterOpener->updateFilterOpener($html);
        }

        return $html;
    }

    /**
     * @param string $data
     * @return string
     */
    protected function prepareAjaxData($data)
    {
        $data = str_replace('&amp;', '&', $data);
        $data = str_replace('?' . ConfigInterface::AJAX_SUFFIX . '=1&', '?', $data);
        $data = str_replace('?' . ConfigInterface::AJAX_SUFFIX . '=1', '', $data);
        $data = str_replace('&' . ConfigInterface::AJAX_SUFFIX . '=1', '', $data);

        return $data;
    }

    /**
     * @param string $data
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    protected function createResponse($data)
    {
        $response = $this->resultRawFactory->create()
            ->setHeader('Content-type', 'text/plain')
            ->setContents(json_encode($data));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUrl()
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = ['_' => null, ConfigInterface::AJAX_SUFFIX => null];

        $currentUrl = $this->urlBuilder->getUrl('*/*/*', $params);
        $currentUrl = $this->urlService->getPreparedUrl($currentUrl);

        return $currentUrl;
    }
}