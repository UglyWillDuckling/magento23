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



namespace Mirasvit\LayeredNavigation\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Image extends Action
{
    /**
     * @param \Mirasvit\Core\Helper\Cron                     $cronHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime    $date
     * @param \Magento\Framework\Registry                    $registry
     * @param \Magento\Framework\Json\Helper\Data            $jsonEncoder
     * @param \Magento\Backend\App\Action\Context            $context
     */
    public function __construct(
        \Mirasvit\Core\Helper\Cron $cronHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $jsonEncoder,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->cronHelper = $cronHelper;
        $this->date = $date;
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->context = $context;
        $this->backendSession = $context->getSession();
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_setActiveMenu('Magento_Catalog::attributes_attributes');
        $this->_view->getLayout()->getBlock('head');

        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Magento_Catalog::attributes_attributes');
    }
}
