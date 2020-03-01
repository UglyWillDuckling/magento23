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

use Mirasvit\LayeredNavigation\Traits\ElasticQueryTrait;

class Elasticsearch5MapperPlugin
{
    use ElasticQueryTrait;

    /**
     * @param \Magento\Elasticsearch\Elasticsearch5\SearchAdapter\Mapper $subject
     * @param array $query
     * @return array
     */
    public function afterBuildQuery($subject, $searchQuery)
    {
        $searchQuery = $this->prepareElasticQuery($searchQuery);

        return $searchQuery;
    }

}