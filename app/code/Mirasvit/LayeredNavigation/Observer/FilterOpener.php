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



namespace Mirasvit\LayeredNavigation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class FilterOpener implements ObserverInterface
{
    const HORIZONTAL_NAVIGATION_REPLACER = '[[[MHORIZONTALNAVIGATION_MNAVIGATIONPRODUCTLISTWRAPPER]]]';

    public function __construct(
        RequestInterface $request,
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->storeId = $storeManager->getStore()->getStoreId();
    }

    /**
     * @param Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $this->filterOpen($observer);
    }

    /**
     * @param bool|Observer $observer
     * @param bool|\Magento\Framework\App\Response\Http $response
     * @return bool
     */
    public function filterOpen($observer, $response = false)
    {
        if ((!is_object($observer) && !$response)
            || !$this->isAllowedRouteName()) {
            return $response;
        }

        if (!$response) {
            $response = $observer->getResponse();
        }
        if (!is_object($response)) {
            return $response;
        }

        $body = $response->getBody();
        $body = $this->updateIncorrectCommaSeparator($body); //fix %2C
        if ($this->isShowOpenedFilters()) {
            $body = $this->updateFilterOpener($body);
        }
        $response->setBody($body);

        return $response;
    }

    /**
     * @return bool
     */
    public function isAllowedRouteName()
    {
        $routeName = $this->request->getRouteName();
        $allowedRouteName = ['catalog', 'catalogsearch', 'brand', 'all_products_page'];

        if (in_array($routeName, $allowedRouteName)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isShowOpenedFilters()
    {
        return $this->config->isShowOpenedFilters($this->storeId);
    }


    /**
     * @param string $body
     * @return string
     */
    public function updateFilterOpener($body)
    {
        $isHorizontalNavigationEnabled = false;
        $replacedHtml = false;
        if (strpos($body, '<div class="navigation-horizontal">') !== false) {
            $isHorizontalNavigationEnabled = true;
        }
        $body = str_replace('$', '\\$', $body); //fix for $ in preg_replace

        if ($isHorizontalNavigationEnabled) {
            $pattern = '/\<div class="navigation-horizontal"\>(.*?)'
                . str_replace('/', '\/', preg_quote(ConfigInterface::NAV_REPLACER_TAG)) . '/ims';
            preg_match($pattern, $body, $matches);
            if (isset($matches[0]) && $matches[0]) {
                $replacedHtml = $matches[0];
                $body = preg_replace($pattern,
                    self::HORIZONTAL_NAVIGATION_REPLACER,
                    $body
                );
            }
        }

        $body = preg_replace('/\\<div class="filter-options" id="narrow-by-list" data-role="content"(.*?)\\>/',
            '<div class="filter-options" id="narrow-by-list" data-role="content"
            data-mage-init=\'{"accordion":{"openedState": "active", "collapsible": true, "active": "'
            . implode(' ', range(0, 300)) . '", "multipleCollapsible": true}}\'>',
            $body
        );

        if ($replacedHtml) {
            $body = preg_replace('/' . preg_quote(self::HORIZONTAL_NAVIGATION_REPLACER) . '/ims',
                $replacedHtml,
                $body
            );
        }

        $body = str_replace('\\$', '$', $body);

        return $body;
    }

    /**
     * @param string $body
     * @return string
     */
    public function updateIncorrectCommaSeparator($body)
    {
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        if (strpos($currentUrl, '?') !== false) {
            $correctUrl = trim(strstr($currentUrl, '?'), '?');
            $incorrectUrl = str_replace(',', '%2C' ,$correctUrl);
            $incorrectUrlSecond = str_replace('&', '&amp;' ,$incorrectUrl);
            $body = str_replace([$incorrectUrl, $incorrectUrlSecond], $correctUrl, $body);
        }

        return $body;
    }
}
