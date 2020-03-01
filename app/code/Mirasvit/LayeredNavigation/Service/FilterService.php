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

use Mirasvit\LayeredNavigation\Api\Service\FilterServiceInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;

class FilterService implements FilterServiceInterface
{
    use ConfigTrait;

    /**
     * @var null|array
     */
    protected static $activeFilters = null;

    /**
     * @var null|array
     */
    protected static $activeFiltersArray = null;

    /**
     * @param LayerResolver $layerResolver
     */
    public function __construct(
        LayerResolver $layerResolver,
        FilterClearBlockConfigInterface $filterClearBlockConfig
    ) {
        $this->filterClearBlockConfig = $filterClearBlockConfig;
        $this->layerResolver = $layerResolver->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveFilters() {

        if (self::$activeFilters === null) {
            self::$activeFilters = $this->layerResolver->getState()->getFilters();
        }

        return (self::$activeFilters === null || !is_array(self::$activeFilters)) ? [] : self::$activeFilters;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterChecked($filterItem, $multiselect)
    {
        if ($multiselect && ConfigTrait::isMultiselectEnabled()) {
            return $this->isMultiselectFilterChecked($filterItem);
        }

        if ($filterItem->getFilter()->getRequestVar() == 'cat') {
            return $filterItem->getValue() == $this->layerResolver->getCurrentCategory()->getId();
        } else {
            $activeFilters = $this->getActiveFilters();
            $attributeCode = $filterItem->getFilter()->getRequestVar();
            $attributeValue = $filterItem->getValueString();

            if (isset($activeFilters[$attributeCode])
                && $activeFilters[$attributeCode] == $attributeValue
            ) {
                return true;
            }
        }

        return false;
    }

    public function isMultiselectFilterChecked($filterItem)
    {
        $activeFilters = $this->getActiveFilters();
        $attributeCode = $filterItem->getFilter()->getRequestVar();
        $attributeValue = (string)$filterItem->getValueString();
        foreach ($activeFilters as $key => $filter) {
            $values = explode(',', $filter->getValueString());

            if ($filter->getFilter()->getRequestVar() == $attributeCode
                && in_array($attributeValue, $values, true)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterCheckedSwatch($attributeCode, $option)
    {
        if (ConfigTrait::isMultiselectEnabled()) {
            $activeFilters = $this->getActiveFilters();
            foreach ($activeFilters as $key => $filter) {
                if ($filter->getFilter()->getRequestVar() == $attributeCode
                    && ($filter->getValueString() == $option
                    || $this->inOneRowExist($filter->getValueString(), $option))
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $filterValueString
     * @param string $option
     * @return bool
     */
    private function inOneRowExist($filterValueString, $option)
    {
        if ($this->filterClearBlockConfig->isFilterClearBlockInOneRow()) {
            $filterValueStringArray = explode(',', $filterValueString);
            if (array_search($option, $filterValueStringArray) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode($filter)
    {
        $attribute = $filter->getData('attribute_model');
        $attributeCode = is_object($attribute)
            ? $attribute->getAttributeCode()
            : substr(get_class($filter), strrpos(get_class($filter), '\\') + 1);

        return $attributeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterUniqueValue($filter)
    {
        return $filter->getRequestVar() . '_' . $filter->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveFiltersArray() {
        if (self::$activeFiltersArray === null) {
            $activeFilters = [];
            foreach ($this->getActiveFilters() as $item) {
                $filter = $item->getFilter();
                if(isset($activeFilters[$filter->getRequestVar()])) {
                    $activeFilters[$filter->getRequestVar()] .= ',' . $item->getValue();
                } else {
                    $activeFilters[$filter->getRequestVar()] = $item->getValue();
                }
            }
            self::$activeFiltersArray = $activeFilters;
        }

        return self::$activeFiltersArray;
    }
}