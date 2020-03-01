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
 * @package   mirasvit/module-search-elastic
 * @version   1.2.45
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Model\Indexer;

use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\Request\IndexScopeResolverInterface;

/**
 * Resolves name of a temporary table for indexation
 */
class TemporaryResolver implements IndexScopeResolverInterface
{
    static $suffix = null;
    /**
     * @var IndexScopeResolver
     */
    private $indexScopeResolver;

    /**
     * @inheritDoc
     */
    public function __construct(IndexScopeResolver $indexScopeResolver)
    {
        $this->indexScopeResolver = $indexScopeResolver;

        self::$suffix = '_tmp';
    }

    /**
     * @param string $index
     * @param Dimension[] $dimensions
     * @return string
     */
    public function resolve($index, array $dimensions)
    {
        $indexName = $this->indexScopeResolver->resolve($index, $dimensions);
        $indexName .= self::$suffix;

        return $indexName;
    }
}
