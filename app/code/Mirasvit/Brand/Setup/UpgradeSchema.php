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



namespace Mirasvit\Brand\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function __construct(
        WriterInterface $configWriter
    ) {
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            include_once 'Upgrade_1_0_1.php';

            Upgrade_1_0_1::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            include_once 'Upgrade_1_0_2.php';

            Upgrade_1_0_2::upgrade($installer, $context);
            $this->configWriter->save('brand/brand_slider/WidgetCode',
                '{{widget type="Mirasvit\Brand\Block\Widget\BrandSlider" template="widget/brand_slider.phtml"}}'
            );
        }

        $installer->endSetup();
    }
}