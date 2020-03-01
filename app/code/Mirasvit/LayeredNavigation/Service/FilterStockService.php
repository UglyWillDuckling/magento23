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

use Mirasvit\LayeredNavigation\Api\Service\FilterStockServiceInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;

class FilterStockService implements FilterStockServiceInterface
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
        StoreManagerInterface $storeManager
    ) {
        $this->scopeResolver = $scopeResolver;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->request = $request;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createStockFilterSelect($currentScope, $entityIdsTable)
    {
        $derivedTable = $this->resourceConnection->getConnection()->select()->from(
            ['main_table' => $this->resourceConnection->getTableName('cataloginventory_stock_status')],
            ['value' => 'stock_status']
        )->joinInner(
            ['entity' => $entityIdsTable->getName()],
            'main_table.product_id  = entity.entity_id',
            []
        );

        $select = $this->resourceConnection->getConnection()->select();
        $select->from(['main_table' => $derivedTable]);

        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentScope($dimensions)
    {
        return $this->scopeResolver->getScope($dimensions['scope']->getValue())->getId();

    }

    /**
     * {@inheritdoc}
     */
    public function addStockToSelect($select)
    {
        $stockFilter = AdditionalFiltersConfigInterface::STOCK_FILTER . '_filter';
        $select->joinLeft(
            [$stockFilter => $this->resourceConnection->getTableName('cataloginventory_stock_status')],
            'search_index.entity_id = ' . $stockFilter . '.product_id'
            . ' AND ' . $stockFilter . '.website_id IN (0, ' . $this->storeManager->getWebsite()->getId() . ')',
            []
        );
    }

}