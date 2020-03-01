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



namespace Mirasvit\Brand\Controller\Brand;

use Mirasvit\Brand\Api\Config\BrandPageConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Session;
use Mirasvit\Brand\Api\Repository\BrandRepositoryInterface;
use Mirasvit\Brand\Api\Service\BrandPageMetaServiceInterface;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var Session
     */
    private $catalogSession;
    /**
     * @var BrandPageMetaServiceInterface
     */
    private $brandPageMetaService;
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    public function __construct(
        BrandRepositoryInterface $brandRepository,
        Registry $registry,
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        Session $catalogSession,
        BrandPageMetaServiceInterface $brandPageMetaService
    ) {
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->catalogSession = $catalogSession;
        $this->brandPageMetaService = $brandPageMetaService;
        $this->brandRepository = $brandRepository;

        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $this->initBrand();
        $this->initCategory();
        $resultPage = $this->resultPageFactory->create();

        return $this->brandPageMetaService->apply($resultPage);
    }

    public function initCategory()
    {
        $categoryId = $this->storeManager->getStore()->getRootCategoryId();

        if (!$categoryId) {
            return false;
        }

        try {
            $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
            $category->setData('is_anchor', 1);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }

        $this->catalogSession->setLastVisitedCategoryId($category->getId());
        if (!$this->registry->registry('current_category')) {
            $this->registry->register('current_category', $category);
        }


        if (!$this->registry->registry(BrandPageConfigInterface::BRAND_DATA)) {
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward
                ->setModule('cms')
                ->setController('noroute')
                ->forward('index');
        }

        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                ['category' => $category]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e);
            return false;
        }

        return $category;
    }

    /**
     * @return $this
     */
    private function initBrand()
    {
        $brandOptionId = $this->getRequest()->getParam('attribute_option_id');
        $brand = $this->brandRepository->get($brandOptionId);

        $this->registry->register(BrandPageConfigInterface::BRAND_DATA, [
            BrandPageConfigInterface::BRAND_ATTRIBUTE => $brand->getAttributeCode(),
            BrandPageConfigInterface::ATTRIBUTE_OPTION_ID => $brandOptionId,
            BrandPageConfigInterface::BRAND_URL_KEY => $brand->getUrl(),
            BrandPageConfigInterface::BRAND_DEFAULT_NAME => $brand->getLabel(),
            BrandPageConfigInterface::BRAND_PAGE_ID => $brand->getPage()->getId()
        ], true);

        return $this;
    }
}
