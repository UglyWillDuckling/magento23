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
 * @version   1.0.45
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Preference;

use Magento\CatalogSearch\Model\Search\FilterMapper\CustomAttributeFilter;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\DB\Select;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogSearch\Model\Adapter\Mysql\Filter\AliasResolver;
use Magento\Catalog\Model\Product;

use Mirasvit\LayeredNavigation\Preference\ChangeAttributeFilterSelectAbstract;

// M 2.1 compatibility
if (class_exists(CustomAttributeFilter::class)) {
    abstract class ChangeAttributeFilterSelectAbstract extends CustomAttributeFilter {}
} else {
    abstract class ChangeAttributeFilterSelectAbstract {}
}

/**
 * fix Magento bug "We can't find products matching the selection."
 * Plugin creates an error
 */
class ChangeAttributeFilterSelect extends ChangeAttributeFilterSelectAbstract
{

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ConditionManager
     */
    protected $conditionManager;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AliasResolver
     */
    protected $aliasResolver;

    /**
     * @param ResourceConnection $resourceConnection
     * @param ConditionManager $conditionManager
     * @param EavConfig $eavConfig
     * @param StoreManagerInterface $storeManager
     * @param AliasResolver $aliasResolver
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ConditionManager $conditionManager,
        EavConfig $eavConfig,
        StoreManagerInterface $storeManager,
        AliasResolver $aliasResolver
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->conditionManager = $conditionManager;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->aliasResolver = $aliasResolver;
    }

    /**
     * Applies filters by custom attributes to base select
     *
     * @param Select $select
     * @param FilterInterface[] ...$filters
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    public function apply(Select $select, FilterInterface ... $filters)
    {
        $select = clone $select;
        $mainTableAlias = $this->extractTableAliasFromSelect($select);
        $attributes = [];

        foreach ($filters as $filter) {
            $filterJoinAlias = $this->aliasResolver->getAlias($filter);

            $attributeId = $this->getAttributeIdByCode($filter->getField());

            if ($attributeId === null) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid attribute id for field: %s', $filter->getField())
                );
            }

            $attributes[] = $attributeId;

            $select->joinInner(
                [$filterJoinAlias => $this->resourceConnection->getTableName('catalog_product_index_eav')],
                $this->conditionManager->combineQueries(
                    $this->getJoinConditions($attributeId, $mainTableAlias, $filterJoinAlias),
                    Select::SQL_AND
                ),
                []
            );
        }

        if (count($attributes) === 1) {
            // forces usage of PRIMARY key in main table
            // is required to boost performance in case when we have just one filter by custom attribute
            $attribute = reset($attributes);
            $filter = reset($filters);
            $select->where(
                $this->conditionManager->generateCondition(
                    sprintf('%s.attribute_id', $mainTableAlias),
                    '=',
                    $attribute
                )
            )->where(
                $this->conditionManager->generateCondition(
                    sprintf('%s.value', $mainTableAlias),
                    is_array($filter->getValue()) ? 'in' : '=',
                    $filter->getValue()
                )
            );
        }

        return $select;
    }

    /**
     * Returns Joins conditions for table catalog_product_index_eav
     *
     * @param int $attrId
     * @param string $mainTable
     * @param string $joinTable
     * @return array
     */
    private function getJoinConditions($attrId, $mainTable, $joinTable)
    {
        return [
            sprintf('`%s`.`entity_id` = `%s`.`entity_id`', $mainTable, $joinTable),
            //don't use entity_id OR source_id, because of very slow load
            // don't use source_id, because of "We can't find products matching the selection."
            //sprintf('`%s`.`source_id` = `%s`.`source_id`', $mainTable, $joinTable) .
            $this->conditionManager->generateCondition(
                sprintf('%s.attribute_id', $joinTable),
                '=',
                $attrId
            ),
            $this->conditionManager->generateCondition(
                sprintf('%s.store_id', $joinTable),
                '=',
                (int) $this->storeManager->getStore()->getId()
            )
        ];
    }

    /**
     * Returns attribute id by code
     *
     * @param string $field
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributeIdByCode($field)
    {
        $attr = $this->eavConfig->getAttribute(Product::ENTITY, $field);

        return ($attr && $attr->getId()) ? (int) $attr->getId() : null;
    }

    /**
     * Extracts alias for table that is used in FROM clause in Select
     *
     * @param Select $select
     * @return string|null
     * @throws \Zend_Db_Select_Exception
     */
    private function extractTableAliasFromSelect(Select $select)
    {
        $fromArr = array_filter(
            $select->getPart(Select::FROM),
            function ($fromPart) {
                return $fromPart['joinType'] === Select::FROM;
            }
        );

        return $fromArr ? array_keys($fromArr)[0] : null;
    }


}
