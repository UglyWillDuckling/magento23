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



namespace Mirasvit\LayeredNavigation\Setup;

use Magento\Variable\Model\VariableFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Mirasvit\LayeredNavigation\Api\Data\CssVariableInterface;

class InstallData implements InstallDataInterface, CssVariableInterface
{
    /**
     * @var VariableFactory $variableFactory
     */
    protected $variableFactory;

    public function __construct(VariableFactory $variableFactory)
    {
        $this->variableFactory = $variableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $variable = $this->variableFactory->create();
        $data = [
            'code' => CssVariableInterface::CSS_VARIABLE,
            'name' => CssVariableInterface::CSS_VARIABLE,
            'html_value' => '',
            'plain_value' => '',
        ];
        $variable->setData($data);
        $variable->save();
    }
}