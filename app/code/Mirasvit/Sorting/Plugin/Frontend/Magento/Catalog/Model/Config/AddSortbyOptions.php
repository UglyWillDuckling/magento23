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



namespace Mirasvit\Sorting\Plugin\Frontend\Magento\Catalog\Model\Config;

use Magento\Catalog\Model\Config;
use Mirasvit\Sorting\Model\Config\Source\Criteria as CriteriaSource;

class AddSortbyOptions
{
    /**
     * @var CriteriaSource
     */
    private $criteria;

    public function __construct(CriteriaSource $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Add Smart Sorting criteria to default "sort by" options.
     *
     * @param Config $subject
     * @param array  $result
     *
     * @return array
     */
    public function afterGetAttributeUsedForSortByArray(Config $subject, array $result = [])
    {
        return $this->criteria->toArray();
    }
}
