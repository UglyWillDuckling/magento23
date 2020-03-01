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



namespace Mirasvit\LayeredNavigation\Service;

use Mirasvit\LayeredNavigation\Api\Service\FilterOnSaleServiceInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Catalog\Model\Product\Visibility;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;
use Magento\Framework\App\RequestInterface;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;

class FilterOnSaleService implements FilterOnSaleServiceInterface
{
    /**
     * @param LayerResolver $layerResolver
     */
    public function __construct(
        ScopeResolverInterface $scopeResolver,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        RequestInterface $request,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession
    ) {
        $this->scopeResolver = $scopeResolver;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->request = $request;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->websiteId = ($storeManager->getStore()->getWebsiteId()) ? : 1;
        $this->customerGroupId = ($customerSession->getCustomerGroupId()) ? : 0;

    }


    /**
     * {@inheritdoc}
     */
    public function createOnSaleFilterSelect($currentScope, $entityIdsTable)
    {
        $productCollection = $this->productCollectionFactory->create()
            ->setVisibility($this->getProductVisibility())
            ->addStoreFilter($currentScope);

        $this->addOnSaleFilterInCollection($productCollection);
        $productCollection->getSelect()->reset(\Zend_Db_Select::COLUMNS);
        $productCollection->getSelect()->columns('e.entity_id');

        $connection = $this->resourceConnection->getConnection();
        $derivedTable = $connection->select();
        $derivedTable->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $derivedTable->joinLeft(
            [AdditionalFiltersConfigInterface::ON_SALE_FILTER => $productCollection->getSelect()],
             AdditionalFiltersConfigInterface::ON_SALE_FILTER . '.entity_id  = entities.entity_id',
            [
                'value' => new \Zend_Db_Expr('if('
                    . AdditionalFiltersConfigInterface::ON_SALE_FILTER . '.entity_id is null, 0, 1)')
            ]
        );

        $select = $connection->select()->from(['main_table' => $derivedTable]);

        return $select;
    }

    /**
     * {@inheritdoc}
     */
    protected function getProductVisibility()
    {
        if ($this->request->getRouteName() == ConfigInterface::IS_CATALOG_SEARCH) {
            return $this->catalogProductVisibility->getVisibleInSearchIds();
        }

        return $this->catalogProductVisibility->getVisibleInCatalogIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentScope($dimensions)
    {
        return $this->scopeResolver->getScope($dimensions['scope']->getValue())->getId();

    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return \Magento\Framework\DB\Select
     */
    public function addOnSaleFilterInCollection($productCollection)
    {
        $productCollection->addPriceData();
        $productCollectionAdvancedPricing = clone $productCollection;
        $productCollectionConfigurable = clone $productCollection;
        $productCollectionConfigurable->addFieldToFilter('type_id', 'configurable');
        $productCollectionUnion = clone $productCollection;

        $selectAdvancedPricing = $this->getAdvancedPricingSelect($productCollectionAdvancedPricing);
        $selectCatalogRule = $this->getCatalogRuleSelect($productCollectionConfigurable);

        $selectUnion = $this->getUnionSelect($productCollectionUnion, $selectAdvancedPricing, $selectCatalogRule);

        $selectOnSale =  $productCollection->getSelect()->joinRight(
            ['sel_merge' => new \Zend_Db_Expr('('.$selectUnion->__toString().')')],
            'e.entity_id = sel_merge.entity_id',
            []
        );

        return $selectOnSale;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionAdvancedPricing
     * @return \Magento\Framework\DB\Select
     */
    private function getAdvancedPricingSelect($productCollectionAdvancedPricing)
    {
        $selectAdvancedPricing = $productCollectionAdvancedPricing->getSelect();
        $selectAdvancedPricing->joinLeft(
            ['catalog_rule' => $productCollectionAdvancedPricing->getTable('catalogrule_product_price')],
            'catalog_rule.product_id = e.entity_id
            AND catalog_rule.website_id = ' . $this->websiteId . '
            AND catalog_rule.customer_group_id = ' . $this->customerGroupId . '
            AND (catalog_rule.latest_start_date < NOW()
            OR catalog_rule.latest_start_date IS NULL)
            AND (catalog_rule.earliest_end_date > NOW()
            OR catalog_rule.earliest_end_date IS NULL)',
            []
        )->where('ifnull(catalog_rule.rule_price, price_index.final_price) < price_index.price')
            ->group('e.entity_id');

        $productCollectionAdvancedPricing
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToFilter('special_from_date', [['lteq' => date('Y-m-d H:i:s', time())], ['null' => true]])
            ->addAttributeToFilter('special_to_date', [['gteq' => date('Y-m-d H:i:s', time())], ['null' => true]]);

        return $selectAdvancedPricing;
    }

    /**
     * Commented code need for debug
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionConfigurable
     * @return \Magento\Framework\DB\Select
     */
    private function getCatalogRuleSelect($productCollectionConfigurable)
    {
        $query = 'SELECT rule_price, product_id AS product_id_child,
                  rule_date AS rule_date_child, customer_group_id, website_id
                  FROM ' . $this->resourceConnection->getTableName('catalogrule_product_price')
                  . '  WHERE rule_date = (SELECT MAX(rule_date)
                  FROM ' . $this->resourceConnection->getTableName('catalogrule_product_price') . ')';

        $selectChildCatalogRule = $this->resourceConnection->getConnection()->select()->from(
            ['cpr' => $this->resourceConnection->getTableName('catalog_product_relation')],
            ['parent_id'  /*, new \Zend_Db_Expr("ANY_VALUE(`ce`.`child_id`)")*/]
        )->joinLeft(
            ['cpp' => new \Zend_Db_Expr('('.$query.')')],
            'cpp.product_id_child = cpr.child_id
            AND cpp.website_id = ' . $this->websiteId . '
            AND cpp.customer_group_id = ' . $this->customerGroupId,
            []
//            [new \Zend_Db_Expr("ANY_VALUE(`cpp`.`rule_price`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`cpp`.`customer_group_id`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`cpp`.`website_id`)")
//            ]
        )->where('rule_price IS NOT NULL'
        )->joinLeft(
            ['conf' => $productCollectionConfigurable->getSelect()],
            'conf.entity_id = cpr.parent_id' ,
            []
//            [new \Zend_Db_Expr("ANY_VALUE(`conf`.`entity_id`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`conf`.`price`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`conf`.`tax_class_id`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`conf`.`final_price`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`conf`.`minimal_price`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`conf`.`min_price`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`conf`.`max_price`)"),
//                new \Zend_Db_Expr("ANY_VALUE(`conf`.`tier_price`)")
//            ]
        )->where('cpp.rule_price < conf.final_price'
        )->group('cpr.parent_id');

        // stub, to make collections equal when unioning them
        $productCollectionConfigurable->getSelect()->columns([
            'special_to_date' => new \Zend_Db_Expr(1),
            'special_from_date' => new \Zend_Db_Expr(1)
        ]);

        $selectCatalogRule = $productCollectionConfigurable->getSelect()->joinLeft(
            ['ccr' => new \Zend_Db_Expr('('.$selectChildCatalogRule->__toString().')')],
            'ccr.parent_id = e.entity_id',
            []
        )->where('ccr.parent_id IS NOT NULL');

        return $selectCatalogRule;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionUnion
     * @param \Magento\Framework\DB\Select $selectAdvancedPricing
     * @param \Magento\Framework\DB\Select $selectCatalogRule
     * @return \Magento\Framework\DB\Select
     */
    private function getUnionSelect($productCollectionUnion, $selectAdvancedPricing, $selectCatalogRule)
    {
        $selectStringAdvancedPricing = $selectAdvancedPricing->__toString();
        $selectStringCatalogRule = $selectCatalogRule->__toString();
        $selectUnion = $productCollectionUnion->getSelect()->reset()->union(
            [new \Zend_Db_Expr('('.$selectStringAdvancedPricing.')'),
                new \Zend_Db_Expr('('.$selectStringCatalogRule.')')],
            \Magento\Framework\DB\Select::SQL_UNION_ALL
        );

        return $selectUnion;
    }

    /**
     * {@inheritdoc}
     */
    public function addOnSaleToSelect($select, $storeId, $onSaleFilterValue)
    {
        //show only correct filters (avoid "We can't find products matching the selection.")
        if ($onSaleFilterValue == 1) {
            $productCollectionOnSale = $this->productCollectionFactory->create()
                ->setVisibility($this->getProductVisibility())
                ->addStoreFilter($storeId);

            $this->addOnSaleFilterInCollection($productCollectionOnSale);
            $productCollectionOnSale->getSelect()->reset(\Zend_Db_Select::COLUMNS);
            $productCollectionOnSale->getSelect()->columns('e.entity_id');

            $onSaleFilter = AdditionalFiltersConfigInterface::ON_SALE_FILTER . '_filter';
            $select->joinRight(
                [$onSaleFilter => $productCollectionOnSale->getSelect()],
                'search_index.entity_id = ' . $onSaleFilter . '.entity_id'
                ,
                []
            );
        }
    }
}