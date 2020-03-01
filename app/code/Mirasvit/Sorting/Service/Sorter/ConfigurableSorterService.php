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



namespace Mirasvit\Sorting\Service\Sorter;

use Mirasvit\Sorting\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Sorter places products with type_id equal to "configurable" to top of the list.
 *
 * Class ConfigurableSorterService
 * @package Mirasvit\Sorting\Service\Sorter
 */
class ConfigurableSorterService
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function sort(Collection $collection)
    {
        if ($this->config->isShowConfigurableFirst()) {
             $collection->getSelect()->order('IF(e.type_id = "configurable", 1, 0) DESC');
        }
    }
}
