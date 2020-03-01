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

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Attribute setting table
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable(AttributeSettingsInterface::TABLE_NAME)
        )->addColumn(
            AttributeSettingsInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            AttributeSettingsInterface::ATTRIBUTE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Id'
        )->addColumn(
            AttributeSettingsInterface::IS_SLIDER,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 0],
            'Is slider'
        )->addColumn(
            AttributeSettingsInterface::ATTRIBUTE_CODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Attribute code'
        )->addColumn(
            AttributeSettingsInterface::IMAGE_OPTIONS,
            Table::TYPE_TEXT,
            1024,
            ['nullable' => true],
            'Image'
        )->addColumn(
            AttributeSettingsInterface::FILTER_TEXT,
            Table::TYPE_TEXT,
            1024,
            ['nullable' => true],
            'Menu text'
        )->addColumn(
            AttributeSettingsInterface::IS_WHOLE_WIDTH_IMAGE,
            Table::TYPE_TEXT,
            1024,
            ['nullable' => true],
            'Whole width picture'
        )->addForeignKey(
            $installer->getFkName(
                AttributeSettingsInterface::TABLE_NAME,
                AttributeSettingsInterface::ATTRIBUTE_ID,
                'eav_attribute',
                'attribute_id'
            ),
            AttributeSettingsInterface::ATTRIBUTE_ID,
            $installer->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Attribute setting table'
        );

        $installer->getConnection()->dropTable($installer->getTable(AttributeSettingsInterface::TABLE_NAME));
        $installer->getConnection()->createTable($table);

        //Mirasvit Layered Navigation Rating
        $installer->getConnection()->addIndex(
            $installer->getTable('review_entity_summary'),
            'mirasvit_layered_navigation_rating',
            ['entity_type', 'entity_pk_value', 'store_id']
        );

    }
}