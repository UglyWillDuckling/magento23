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
 * @package   mirasvit/module-search-elastic
 * @version   1.2.45
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchElastic\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\SearchElastic\Model\Engine;

abstract class Command extends Action
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        Context $context,
        Engine $engine
    ) {
        $this->engine = $engine;
        $this->context = $context;

        parent::__construct($context);
    }
}
