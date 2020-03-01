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



namespace Mirasvit\LayeredNavigation\Api\Service;

interface FilterServiceInterface
{
    /**
     * @return null| array
     */
    public function getActiveFilters();

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @param bool $multiselect
     * @return bool
     */
    public function isFilterChecked($filterItem, $multiselect);

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function getAttributeCode($filter);

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function getFilterUniqueValue($filter);

    /**
     * @return array
     */
    public function getActiveFiltersArray();
}