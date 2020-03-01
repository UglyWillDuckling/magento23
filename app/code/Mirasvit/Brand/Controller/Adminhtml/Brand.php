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



namespace Mirasvit\Brand\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Model\Brand\PostData\Processor as PostDataProcessor;
use Mirasvit\Brand\Api\Config\ConfigInterface;

abstract class Brand extends Action
{
    public function __construct(
        BrandPageRepositoryInterface $brandPageRepository,
        Context $context,
        PostDataProcessor $postDataProcessor,
        ConfigInterface $config
    ) {
        $this->brandPageRepository = $brandPageRepository;
        $this->context = $context;
        $this->postDataProcessor = $postDataProcessor;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Backend::content');
        $resultPage->getConfig()->getTitle()->prepend(__('Brand Pages'));

        return $resultPage;
    }

    /**
     * @return BrandPageInterface
     */
    public function initModel()
    {
        $model = $this->brandPageRepository->create();

        if ($this->getRequest()->getParam('id')) {
            $model = $this->brandPageRepository->get($this->getRequest()->getParam('id'));
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Brand::brand_brand');
    }
}
