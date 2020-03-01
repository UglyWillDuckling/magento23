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



namespace Mirasvit\LayeredNavigation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFilterOptionsInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as AttributeCollection;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;

class HorizontalFilterOptions implements ArrayInterface, HorizontalFilterOptionsInterface
{
    /**
     * @param AttributeCollection $attributeCollection
     */
    public function __construct(
        AttributeCollection $attributeCollection
    ) {
        $this->attributeCollection = $attributeCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => HorizontalFilterOptionsInterface::ALL_FILTERED_ATTRIBUTES,
                'label' => __('All filtered attributes')
            ],
        ];

        return array_merge($options, $this->getFilteredAttributesOptions());
    }

    /**
     * @return array
     */
    private function getFilteredAttributesOptions()
    {
        $filteredAttributesOptions = [];
        $collection = clone $this->attributeCollection; //need use clear collection for next usage
        $collection
            ->join(
            ['cea' => $collection->getTable('catalog_eav_attribute')],
            'cea.attribute_id = main_table.attribute_id',
            []
        )->addFieldToFilter('cea.is_filterable', 1);

        foreach ($collection->getItems() as $item) {
            $filteredAttributesOptions[] = [
                'value' => $item->getAttributeCode()
                    . HorizontalFilterOptionsInterface::HORIZONTAL_FILTER_CONFIG_SEPARATOR
                    . $item->getId(),
                'label' => $item->getFrontendLabel()
            ];
        }

        $filteredAdditionalAttributesOptions = $this->getAdditionalFilters();

        $filteredAttributesOptions = array_merge($filteredAttributesOptions,
            $filteredAdditionalAttributesOptions
        );

        return $filteredAttributesOptions;
    }

    /**
     * @return array
     */
    private function getAdditionalFilters()
    {
        $filteredAdditionalAttributesOptions = [];

        $filteredAdditionalAttributesOptions[] = [
            'value' => AdditionalFiltersConfigInterface::NEW_FILTER_FRONT_PARAM
                . HorizontalFilterOptionsInterface::HORIZONTAL_FILTER_CONFIG_SEPARATOR
                . '1',
            'label' => AdditionalFiltersConfigInterface::NEW_FILTER_DEFAULT_LABEL
        ];

        $filteredAdditionalAttributesOptions[] = [
            'value' => AdditionalFiltersConfigInterface::ON_SALE_FILTER_FRONT_PARAM
                . HorizontalFilterOptionsInterface::HORIZONTAL_FILTER_CONFIG_SEPARATOR
                . '1',
            'label' => AdditionalFiltersConfigInterface::ON_SALE_FILTER_DEFAULT_LABEL
        ];

        $filteredAdditionalAttributesOptions[] = [
            'value' => AdditionalFiltersConfigInterface::STOCK_FILTER_FRONT_PARAM
                . HorizontalFilterOptionsInterface::HORIZONTAL_FILTER_CONFIG_SEPARATOR
                . '1',
            'label' => AdditionalFiltersConfigInterface::STOCK_FILTER_DEFAULT_LABEL
        ];

        $filteredAdditionalAttributesOptions[] = [
            'value' => AdditionalFiltersConfigInterface::RATING_FILTER_FRONT_PARAM
                . HorizontalFilterOptionsInterface::HORIZONTAL_FILTER_CONFIG_SEPARATOR
                . '1',
            'label' => AdditionalFiltersConfigInterface::RATING_FILTER_DEFAULT_LABEL
        ];

        return $filteredAdditionalAttributesOptions;
    }
}
