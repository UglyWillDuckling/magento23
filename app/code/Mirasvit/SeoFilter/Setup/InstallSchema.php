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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;

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
         * SeoFilter
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable(RewriteInterface::TABLE_NAME)
        )->addColumn(
            RewriteInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rewrite Id'
        )->addColumn(
            RewriteInterface::ATTRIBUTE_CODE,
            Table::TYPE_TEXT,
            120,
            ['nullable' => false],
            'Attribute Code'
        )->addColumn(
            RewriteInterface::OPTION_ID,
            Table::TYPE_INTEGER,
            10,
            ['nullable' => false, 'unsigned' => true],
            'Option Id'
        )->addColumn(
            RewriteInterface::PRICE_OPTION_ID,
            Table::TYPE_TEXT,
            120,
            ['nullable' => true, 'unsigned' => true],
            'Price Option Id'
        )->addColumn(
            RewriteInterface::REWRITE,
            Table::TYPE_TEXT,
            120,
            ['nullable' => false],
            'Rewrite'
        )->addColumn(
            RewriteInterface::STORE_ID,
            Table::TYPE_SMALLINT,
            5,
            ['nullable' => false, 'unsigned' => true],
            'Store Id'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable(RewriteInterface::TABLE_NAME),
                [RewriteInterface::ATTRIBUTE_CODE, RewriteInterface::OPTION_ID, RewriteInterface::STORE_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [RewriteInterface::ATTRIBUTE_CODE, RewriteInterface::OPTION_ID, RewriteInterface::STORE_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable(RewriteInterface::TABLE_NAME),
                [RewriteInterface::REWRITE, RewriteInterface::STORE_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [RewriteInterface::REWRITE, RewriteInterface::STORE_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable(RewriteInterface::TABLE_NAME),
                [RewriteInterface::ATTRIBUTE_CODE, RewriteInterface::PRICE_OPTION_ID, RewriteInterface::STORE_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [RewriteInterface::ATTRIBUTE_CODE, RewriteInterface::PRICE_OPTION_ID, RewriteInterface::STORE_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable(RewriteInterface::TABLE_NAME),
                [RewriteInterface::STORE_ID],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [RewriteInterface::STORE_ID],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable(RewriteInterface::TABLE_NAME),
                [RewriteInterface::OPTION_ID],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [RewriteInterface::OPTION_ID],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable(RewriteInterface::TABLE_NAME),
                [RewriteInterface::PRICE_OPTION_ID],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [RewriteInterface::PRICE_OPTION_ID],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addForeignKey(
            $installer->getFkName(
                'store',
                'store_id',
                RewriteInterface::TABLE_NAME,
                RewriteInterface::STORE_ID
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'eav_attribute_option',
                'option_id',
                RewriteInterface::TABLE_NAME,
                RewriteInterface::STORE_ID
            ),
            'option_id',
            $installer->getTable('eav_attribute_option'),
            'option_id',
            Table::ACTION_CASCADE
        )->setComment(
            'SeoFilter'
        );

        $installer->getConnection()->dropTable($installer->getTable(RewriteInterface::TABLE_NAME));
        $installer->getConnection()->createTable($table);
    }
}
