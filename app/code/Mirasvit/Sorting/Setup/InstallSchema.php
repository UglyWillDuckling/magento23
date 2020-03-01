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
 * @package   mirasvit/module-sorting
 * @version   1.0.9
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Sorting\Setup;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\Sorting\Criteria\BestsellersCriterion;
use Mirasvit\Sorting\Criteria\DiscountCriterion;
use Mirasvit\Sorting\Criteria\WishedCriterion;
use Mirasvit\Sorting\Criteria\ViewsCriterion;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $tables = [];

        $installer->startSetup();

        $tables[] = $this->createBestsellersTable($installer);
        $tables[] = $this->createDiscountTable($installer);
        $tables[] = $this->createWishedTable($installer);
        $tables[] = $this->createViewsTable($installer);

        foreach ($tables as $table) {
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createBestsellersTable(SchemaSetupInterface $installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable(BestsellersCriterion::TABLE_NAME)
        )->addColumn(
            BestsellersCriterion::INDEX_ID,
            Table::TYPE_INTEGER,
            null,
            ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
            'Index Id'
        )->addColumn(
            BestsellersCriterion::PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )->addColumn(
            BestsellersCriterion::STORE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            BestsellersCriterion::COUNT,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Product count'
        )->addColumn(
            BestsellersCriterion::QTY_ORDERED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => 0],
            'Qty Ordered'
        )->addColumn(
            BestsellersCriterion::SALES_AMOUNT,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => 0],
            'Sales Amount'
        )->addColumn(
            BestsellersCriterion::SALES_RATIO,
            Table::TYPE_DECIMAL,
            '5,2',
            ['nullable' => false, 'default' => 0],
            'Sales Ration'
        )->addForeignKey(
            $installer->getFkName(
                BestsellersCriterion::TABLE_NAME,
                BestsellersCriterion::PRODUCT_ID,
                'catalog_product_entity',
                'entity_id'
            ),
            BestsellersCriterion::PRODUCT_ID,
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                BestsellersCriterion::TABLE_NAME,
                BestsellersCriterion::STORE_ID,
                'store',
                'store_id'
            ),
            BestsellersCriterion::STORE_ID,
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                BestsellersCriterion::TABLE_NAME,
                [BestsellersCriterion::PRODUCT_ID, BestsellersCriterion::STORE_ID],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [BestsellersCriterion::PRODUCT_ID, BestsellersCriterion::STORE_ID],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDiscountTable(SchemaSetupInterface $installer)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '<')) {
            $customerGroupIdType = Table::TYPE_SMALLINT;
        } else {
            $customerGroupIdType = Table::TYPE_INTEGER;
        }

        return $installer->getConnection()->newTable(
            $installer->getTable(DiscountCriterion::TABLE_NAME)
        )->addColumn(
            DiscountCriterion::INDEX_ID,
            Table::TYPE_INTEGER,
            null,
            ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
            'Index Id'
        )->addColumn(
            DiscountCriterion::PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )->addColumn(
            DiscountCriterion::WEBSITE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website Id'
        )->addColumn(
            DiscountCriterion::CUSTOMER_GROUP_ID,
            $customerGroupIdType,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Group Id'
        )->addColumn(
            DiscountCriterion::DISCOUNT_PERCENT,
            Table::TYPE_DECIMAL,
            '5,2',
            ['nullable' => false, 'default' => 0],
            'Discount percent'
        )->addColumn(
            DiscountCriterion::DISCOUNT_AMOUNT,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => 0],
            'Discount percent'
        )->addForeignKey(
            $installer->getFkName(
                DiscountCriterion::TABLE_NAME,
                DiscountCriterion::PRODUCT_ID,
                'catalog_product_entity',
                'entity_id'
            ),
            DiscountCriterion::PRODUCT_ID,
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                DiscountCriterion::TABLE_NAME,
                DiscountCriterion::WEBSITE_ID,
                'store_website',
                'website_id'
            ),
            DiscountCriterion::WEBSITE_ID,
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                DiscountCriterion::TABLE_NAME,
                DiscountCriterion::CUSTOMER_GROUP_ID,
                'customer_group',
                'customer_group_id'
            ),
            DiscountCriterion::CUSTOMER_GROUP_ID,
            $installer->getTable('customer_group'),
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                DiscountCriterion::TABLE_NAME,
                [DiscountCriterion::PRODUCT_ID, DiscountCriterion::WEBSITE_ID, DiscountCriterion::CUSTOMER_GROUP_ID],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [DiscountCriterion::PRODUCT_ID, DiscountCriterion::WEBSITE_ID, DiscountCriterion::CUSTOMER_GROUP_ID],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createWishedTable(SchemaSetupInterface $installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable(WishedCriterion::TABLE_NAME)
        )->addColumn(
            WishedCriterion::INDEX_ID,
            Table::TYPE_INTEGER,
            null,
            ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
            'Index Id'
        )->addColumn(
            WishedCriterion::PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )->addColumn(
            WishedCriterion::STORE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            WishedCriterion::COUNT,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Products count in wishlist'
        )->addForeignKey(
            $installer->getFkName(
                WishedCriterion::TABLE_NAME,
                WishedCriterion::PRODUCT_ID,
                'catalog_product_entity',
                'entity_id'
            ),
            WishedCriterion::PRODUCT_ID,
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                WishedCriterion::TABLE_NAME,
                WishedCriterion::STORE_ID,
                'store',
                'store_id'
            ),
            WishedCriterion::STORE_ID,
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                WishedCriterion::TABLE_NAME,
                [WishedCriterion::PRODUCT_ID, WishedCriterion::STORE_ID],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [WishedCriterion::PRODUCT_ID, WishedCriterion::STORE_ID],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createViewsTable(SchemaSetupInterface $installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable(ViewsCriterion::TABLE_NAME)
        )->addColumn(
            ViewsCriterion::INDEX_ID,
            Table::TYPE_INTEGER,
            null,
            ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
            'Index Id'
        )->addColumn(
            ViewsCriterion::PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )->addColumn(
            ViewsCriterion::STORE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            ViewsCriterion::COUNT,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Views Count'
        )->addForeignKey(
            $installer->getFkName(
                ViewsCriterion::TABLE_NAME,
                ViewsCriterion::PRODUCT_ID,
                'catalog_product_entity',
                'entity_id'
            ),
            ViewsCriterion::PRODUCT_ID,
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                ViewsCriterion::TABLE_NAME,
                ViewsCriterion::STORE_ID,
                'store',
                'store_id'
            ),
            ViewsCriterion::STORE_ID,
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                ViewsCriterion::TABLE_NAME,
                [ViewsCriterion::PRODUCT_ID, ViewsCriterion::STORE_ID],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [ViewsCriterion::PRODUCT_ID, ViewsCriterion::STORE_ID],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }
}
