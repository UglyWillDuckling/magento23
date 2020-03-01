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
 * @package   mirasvit/module-search
 * @version   1.0.124
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Search\Api\Data\IndexInterface;
use Mirasvit\Search\Controller\Adminhtml\Index as ParentIndex;

class Edit extends ParentIndex
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $model = $this->initModel();

        if ($this->getRequest()->getParam(IndexInterface::ID)) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This search index no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Edit Search Index "%1"', $model->getTitle()) : __('New Search Index')
        );

        return $resultPage;
    }
}
