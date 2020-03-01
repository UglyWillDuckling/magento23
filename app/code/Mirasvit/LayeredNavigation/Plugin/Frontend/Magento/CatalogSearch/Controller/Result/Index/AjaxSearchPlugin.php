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



namespace Mirasvit\LayeredNavigation\Plugin\Frontend\Magento\CatalogSearch\Controller\Result\Index;

use Mirasvit\LayeredNavigation\Api\Service\AjaxResponseServiceInterface;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;

class AjaxSearchPlugin
{
    use ConfigTrait;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param QueryFactory $queryFactory
     * @param Resolver $layerResolver
     * @param AjaxResponseServiceInterface $ajaxResponseService
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        AjaxResponseServiceInterface $ajaxResponseService
    ) {
        $this->_objectManager = $context->getObjectManager();
        $this->resultFactory = $context->getResultFactory();
        $this->_storeManager = $storeManager;
        $this->_queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
        $this->ajaxResponseService = $ajaxResponseService;
        $this->_response = $context->getResponse();
        $this->_redirect = $context->getRedirect();
        $this->_url = $context->getUrl();
    }

    /**
     * @param \Magento\Catalog\Controller\Category\View $subject
     * @param callable $proceed
     * @return \Magento\Framework\View\Result\Page
     */
    public function aroundExecute($subject, callable $proceed)
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_queryFactory->get();

        $query->setStoreId($this->_storeManager->getStore()->getId());

        if ($query->getQueryText() != '') {
            if ($this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class)->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                $query->saveIncrementalPopularity();

                $redirect = $query->getRedirect();
                if ($redirect && $this->_url->getCurrentUrl() !== $redirect) {
                    $this->getResponse()->setRedirect($redirect);
                    return false;
                }
            }

            $this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class)->checkNotes();

            $page = $this->resultFactory->create('page');
            if ($this->isAllowed($subject->getRequest())) {
                return $this->ajaxResponseService->getAjaxResponse($page);
            }

            return $page;
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }

    /**
     * Retrieve response object
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->_response;
    }
}