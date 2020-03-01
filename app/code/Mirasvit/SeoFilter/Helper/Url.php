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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\Registry;

class Url extends AbstractHelper
{
    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $categoryUrlSuffix = [];

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param RequestHttp $request
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        RequestHttp $request,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->url = $context->getUrlBuilder();
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->registry = $registry;

    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param null|int $storeId
     * @return string
     */
    public function getCategoryUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        if (!isset($this->categoryUrlSuffix[$storeId])) {
            $this->categoryUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->categoryUrlSuffix[$storeId];
    }

    /**
     * Check if category page
     *
     * @return bool
     */
    public function isCategoryPage()
    {
        if ($this->request->getFullActionName() == 'catalog_category_view') {
            return true;
        }

        return false;
    }

    /**
     * Return catalog current category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @param bool|string $url
     * @return string
     */
    public function getQueryParams($url = false)
    {
        $currentUrl =  $this->url->getCurrentUrl();

        if ($url) {
            return strtok($currentUrl, '?') . strstr($url, '?', false);
        }

        return strstr($currentUrl, '?', false);
    }

    /**
     * @param string $url
     * @return string
     */
    public function addUrlParams($url) {
        return $url . $this->getQueryParams();
    }

    /**
     * @param string $url
     * @return string
     */
    public function getStoreId() {
        return $this->storeManager->getStore()->getId();
    }
}