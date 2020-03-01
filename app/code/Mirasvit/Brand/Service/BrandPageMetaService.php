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



namespace Mirasvit\Brand\Service;

use Mirasvit\Brand\Api\Service\BrandPageMetaServiceInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Registry;
use Mirasvit\Brand\Api\Config\BrandPageConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Brand\Api\Service\BrandUrlServiceInterface;

class BrandPageMetaService implements BrandPageMetaServiceInterface
{
    /**
     * @var BrandUrlServiceInterface
     */
    private $brandUrlService;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var BrandPageRepositoryInterface
     */
    private $brandPageRepository;
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var Context
     */
    private $context;

    public function __construct(
        BrandUrlServiceInterface $brandUrlService,
        Registry $registry,
        BrandPageRepositoryInterface $brandPageRepository,
        ConfigInterface $config,
        Context $context
    ) {
        $this->brandUrlService = $brandUrlService;
        $this->registry = $registry;
        $this->storeManager = $context->getStoreManager();
        $this->brandPageRepository = $brandPageRepository;
        $this->config = $config;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle($isIndexPage)
    {
        if ($isIndexPage) {
            return ' ';
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($brandTitle = $this->brandPageRepository->get($brandPageId)->getBrandTitle())) {
            return $brandTitle;
        }

        return $brandPageData[BrandPageConfigInterface::BRAND_DEFAULT_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle($isIndexPage)
    {
        if ($isIndexPage) {
            return ($this->config->getAllBrandPageConfig()->getMetaTitle()) ? : __('Brands');
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($metaTitle = $this->brandPageRepository->get($brandPageId)->getMetaTitle())) {
            return $metaTitle;
        }

        return $brandPageData[BrandPageConfigInterface::BRAND_DEFAULT_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyword($isIndexPage)
    {
        if ($isIndexPage) {
            return $this->config->getAllBrandPageConfig()->getMetaKeyword();
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($metaKeyword = $this->brandPageRepository->get($brandPageId)->getMetaKeyword())) {
            return $metaKeyword;
        }

        return $brandPageData[BrandPageConfigInterface::BRAND_DEFAULT_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription($isIndexPage)
    {
        if ($isIndexPage) {
            return $this->config->getAllBrandPageConfig()->getMetaDescription();
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($metaDescription = $this->brandPageRepository->get($brandPageId)->getMetaDescription())) {
            return $metaDescription;
        }

        return $brandPageData[BrandPageConfigInterface::BRAND_DEFAULT_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonical($isIndexPage)
    {
        if ($isIndexPage) {
            return $this->brandUrlService->getBaseBrandUrl();
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($canonical = $this->brandPageRepository->get($brandPageId)->getCanonical())) {
            if ((strpos('http:',$canonical) !== false) && (strpos('https:',$canonical) !== false)) {
                return $canonical;
            } else {
                return $this->storeManager->getStore()->getBaseUrl() . ltrim($canonical, '/');
            }

        }

        return $this->storeManager->getStore()->getBaseUrl()
            . $this->getDefaultData()[BrandPageConfigInterface::BRAND_URL_KEY];
    }

    /**
     * {@inheritdoc}
     */
    public function getRobots($isIndexPage)
    {
        $indexFollow = 'INDEX,FOLLOW';
        if ($isIndexPage) {
            return $indexFollow;
        }

        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($robots = $this->brandPageRepository->get($brandPageId)->getRobots())) {
            return $robots;
        }

        return $indexFollow;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return $this->registry->registry(BrandPageConfigInterface::BRAND_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandPageId($brandPageData)
    {
        $brandPageId = null;
        if (isset($brandPageData[BrandPageConfigInterface::BRAND_PAGE_ID])) {
            $brandPageId = $brandPageData[BrandPageConfigInterface::BRAND_PAGE_ID];
        }

        return $brandPageId;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Page $page, $isIndexPage = false)
    {
        $pageConfig = $page->getConfig();
        $pageConfig->getTitle()->set(__($this->getMetaTitle($isIndexPage)));
        $pageConfig->setMetadata('description', $this->getMetaDescription($isIndexPage));
        $pageConfig->setMetadata('keyword', $this->getKeyword($isIndexPage));
        $pageConfig->setMetadata('robots', $this->getRobots($isIndexPage));
        $pageConfig->addRemotePageAsset(
            $this->getCanonical($isIndexPage),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
        $layout = $this->context->getLayout();
        if ($pageMainTitle = $layout->getBlock('page.main.title')) {
            $pageMainTitle->setPageTitle($this->getTitle($isIndexPage));
        }

        return $page;
    }
}