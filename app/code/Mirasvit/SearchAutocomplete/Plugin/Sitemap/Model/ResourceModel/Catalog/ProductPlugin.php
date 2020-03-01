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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.94
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Plugin\Sitemap\Model\ResourceModel\Catalog;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Mirasvit\SearchAutocomplete\Index\Magento\Catalog\Product as ProductMapper;

class ProductPlugin
{
	private $productCollection;
	private $productMapper;

	public function __construct (
		ProductCollection $productCollection,
		ProductMapper $productMapper
	){
		$this->productCollection 	= $productCollection;
		$this->productMapper 		= $productMapper;
	}

    public function aroundPrepareSelectStatement($subject, $closure, \Magento\Framework\DB\Select $select)
    {
    	$productIds = $this->productMapper->getProductIds();
    	if (empty($productIds) || !$productIds) {
	        return $closure($select);
    	}

    	$select->columns('sku');
    	$select->where('e.entity_id IN (?)', $productIds);

    	return $closure($select);
    }
}
