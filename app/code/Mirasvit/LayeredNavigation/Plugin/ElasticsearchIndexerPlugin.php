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



namespace Mirasvit\LayeredNavigation\Plugin;

use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Api\Service\VersionServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\ElasticsearchServiceInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;

class ElasticsearchIndexerPlugin
{
    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        VersionServiceInterface $versionService,
        ElasticsearchServiceInterface $elasticsearchService,
        ConfigInterface $config
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
        $this->versionService = $versionService;
        $this->elasticsearchService = $elasticsearchService;
        $this->config = $config;
    }

    public function aroundGetSearchableProducts($subject,
                                                \Closure $proceed,
                                                $storeId,
                                                array $staticFields,
                                                $productIds = null,
                                                $lastProductId = 0,
                                                $limit = 100
    ) {
        if ($this->config->isCorrectElasticFilterCount()
            && $this->versionService->isEe()
            && $this->elasticsearchService->isElasticEnabled()) {
                $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
                $select = $this->connection->select()
                    ->useStraightJoin(true)
                    ->from(
                        ['e' => $this->getTable('catalog_product_entity')],
                        array_merge(['entity_id', 'type_id'], $staticFields)
                    )
                    ->join(
                        ['website' => $this->getTable('catalog_product_website')],
                        $this->connection->quoteInto('website.product_id = e.entity_id AND website.website_id = ?', $websiteId),
                        []
                    );

                if ($productIds !== null) {
                    $select->where('e.entity_id IN (?)', $productIds);
                }

                $select->where('e.entity_id > ?', $lastProductId)->limit($limit)->order('e.entity_id');

                $select->join(
                    [
                        'stock_index' => $this->getTable('cataloginventory_stock_status'),
                    ],
                    'e.entity_id = stock_index.product_id AND stock_index.stock_status = 1',
                    []
                );

                $result = $this->connection->fetchAll($select);

                return $result;
        }

        return $proceed($storeId,
                $staticFields,
                $productIds,
                $lastProductId,
                $limit
        );
    }

    /**
     * Return validated table name
     *
     * @param string|string[] $table
     * @return string
     */
    private function getTable($table)
    {
        return $this->resource->getTableName($table);
    }

}