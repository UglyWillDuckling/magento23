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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Mirasvit\SeoFilter\Api\Data\PriceRewriteInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Upgrade_1_0_1
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public static function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $connection = $setup->getConnection();
            $table = $connection->newTable(
                $setup->getTable(PriceRewriteInterface::TABLE_NAME)
            )->addColumn(
                PriceRewriteInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rewrite Id'
            )->addColumn(
                PriceRewriteInterface::ATTRIBUTE_CODE,
                Table::TYPE_TEXT,
                120,
                ['nullable' => false],
                'Attribute Code'
            )->addColumn(
                PriceRewriteInterface::PRICE_OPTION_ID,
                Table::TYPE_TEXT,
                120,
                ['nullable' => true, 'unsigned' => true],
                'Price Option Id'
            )->addColumn(
                PriceRewriteInterface::REWRITE,
                Table::TYPE_TEXT,
                120,
                ['nullable' => false],
                'Rewrite'
            )->addColumn(
                PriceRewriteInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false, 'unsigned' => true],
                'Store Id'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable(PriceRewriteInterface::TABLE_NAME),
                    [PriceRewriteInterface::REWRITE, PriceRewriteInterface::STORE_ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [PriceRewriteInterface::REWRITE, PriceRewriteInterface::STORE_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable(PriceRewriteInterface::TABLE_NAME),
                    [PriceRewriteInterface::ATTRIBUTE_CODE,
                        PriceRewriteInterface::PRICE_OPTION_ID,
                        PriceRewriteInterface::STORE_ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [PriceRewriteInterface::ATTRIBUTE_CODE,
                    PriceRewriteInterface::PRICE_OPTION_ID,
                    PriceRewriteInterface::STORE_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable(PriceRewriteInterface::TABLE_NAME),
                    [PriceRewriteInterface::STORE_ID],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                [PriceRewriteInterface::STORE_ID],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable(PriceRewriteInterface::TABLE_NAME),
                    [PriceRewriteInterface::PRICE_OPTION_ID],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                [PriceRewriteInterface::PRICE_OPTION_ID],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            )->addForeignKey(
                $setup->getFkName(
                    'store',
                    'store_id',
                    PriceRewriteInterface::TABLE_NAME,
                    PriceRewriteInterface::STORE_ID
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Price SeoFilter'
            );

            $setup->getConnection()->dropTable($setup->getTable(PriceRewriteInterface::TABLE_NAME));
            $setup->getConnection()->createTable($table);
        }
    }
}