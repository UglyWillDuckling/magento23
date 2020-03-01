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



namespace Mirasvit\Brand\Controller\Adminhtml\Brand;

use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Brand\Model\ResourceModel\BrandPage\CollectionFactory;
use Mirasvit\Brand\Controller\Adminhtml\Brand;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Magento\Backend\App\Action\Context;
use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Model\Brand\PostData\Processor as PostDataProcessor;
use Mirasvit\Brand\Api\Config\ConfigInterface;


class MassDelete extends Brand
{
    public function __construct(
        BrandPageRepositoryInterface $brandPageRepository,
        Context $context,
        PostDataProcessor $postDataProcessor,
        ConfigInterface $config,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($brandPageRepository,
            $context,
            $postDataProcessor,
            $config
        );
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $ids = [];

        if ($this->getRequest()->getParam(BrandPageInterface::ID)) {
            $ids = $this->getRequest()->getParam(BrandPageInterface::ID);
        }

        if ($this->getRequest()->getParam(Filter::SELECTED_PARAM)) {
            $ids = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
        }

        if (!$ids) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $ids = $collection->getAllIds();
        }

        if ($ids) {
            try {
                foreach ($ids as $id) {
                    $model = $this->brandPageRepository->get($id);
                    $this->brandPageRepository->delete($model);
                }
                $this->messageManager->addSuccessMessage(
                    __('%1 item(s) was removed', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Please select item(s)'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select item(s)'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
