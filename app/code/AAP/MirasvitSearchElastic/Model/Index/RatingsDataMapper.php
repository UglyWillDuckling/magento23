<?php
namespace AAP\MirasvitSearchElastic\Model\Index;


use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\Request\Dimension;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;
use Mirasvit\Search\Api\Data\Index\DataMapperInterface;

class RatingsDataMapper implements DataMapperInterface
{
    private $relationsData;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(ResourceConnection $resourceConnection, ProductMetadataInterface $productMetadata)
    {
        $this->resourceConnection = $resourceConnection;
        $this->productMetadata = $productMetadata;
    }

    public function map(array $documents, $dimensions, $indexIdentifier)
    {
        $ratings = $this->retrieveRatings($documents, $dimensions);

        foreach ($ratings as $rating) {
            if (isset($documents[$rating['entity_id']])) {
                $documents[$rating['entity_id']]['rating_summary_raw'] = (float)$rating['value'];
            }
        }

        $this->buildRelationsData(array_keys($documents));

        $parentIds = [];
        foreach ($ratings as $rating) {
            if(isset($this->relationsData[$rating['entity_id']]))
            {
                $parentIds[] = $this->relationsData[$id]['parent_id'];
                $documents[$this->relationsData[$id]['parent_id']]['children'][] = ['rating_summary_raw' => $rating['value']];
            }
        }

        foreach (array_diff(array_keys($documents), $parentIds) as $diffKey) {
            if (isset($documents[$diffKey])) {
                $documents[$diffKey]['children'][] = $documents[$diffKey];
            }
        }

        return $documents;
    }

    protected function retrieveRatings($documents, $dimensions)
    {

        $select = $this->resourceConnection->getConnection()->select()->from(
            'review_entity_summary',
            [
                'primary_id',
                'rating_summary as value',
                'entity_pk_value as entity_id'
            ]
        );

        $select->where('entity_pk_value in (?)', array_keys($documents))
               ->where('store_id = (?)', $dimensions['scope']->getValue())
        ;

        $ratings = $this->resourceConnection->getConnection()->fetchAssoc($select);

        return $ratings;
    }

    private function buildRelationsData(array $parentIds)
    {
        if (!is_null($this->relationsData)) {
            return;
        }


        if (empty($parentIds)) {
            return false;
        }

        $select = $this->resourceConnection->getConnection()->select();

        if ($this->productMetadata->getEdition() == 'Enterprise' || $this->productMetadata->getEdition() == 'B2B') {
            $select->from(
                ['relation' => $this->resource->getTableName('catalog_product_relation')],
                ['child_id']
            );

            $select->join(['product_entity' => $this->resource->getTableName('catalog_product_entity')],
                          'product_entity.row_id = relation.parent_id',
                          ['parent_id' => 'product_entity.row_id']
            );

            $select->join(['child_product_entity' => $this->resource->getTableName('catalog_product_entity')],
                          'child_product_entity.entity_id = relation.child_id',
                          []
            );

            $select->group('product_entity.row_id');
        } else {
            $select->from(
                ['relation' => $this->resourceConnection->getTableName('catalog_product_relation')],
                ['child_id', 'parent_id']
            );

            $select->join(['product_entity' => $this->resourceConnection->getTableName('catalog_product_entity')],
                          'product_entity.entity_id = relation.parent_id',
                          []
            );
        }

        $select->where('product_entity.entity_id IN (' . implode(',', $parentIds) . ')');

        $children = $this->resourceConnection->getConnection()->fetchAssoc($select);

        $this->childrenData = array_keys($children);
        $this->relationsData = $children;
    }
}
