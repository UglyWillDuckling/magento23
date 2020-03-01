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
use Mirasvit\LayeredNavigation\Api\Config\SliderConfigInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;

class SliderOptions implements ArrayInterface
{
    private $attributeCollectionFactory;

    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getFilteredAttributesOptions();
    }

    /**
     * @return array
     */
    private function getFilteredAttributesOptions()
    {
        $filteredAttributesOptions = [];
        $collection = $this->attributeCollectionFactory->create();
        $collection
            ->join(
                ['cea' => $collection->getTable('catalog_eav_attribute')],
                'cea.attribute_id = main_table.attribute_id',
                ['is_filterable' => 'cea.is_filterable']
            )->addFieldToFilter(
                ['frontend_input', 'attribute_code', 'frontend_class'],
                [
                    ['eq' => 'price'],
                    null,
                    ['validate-number', 'validate-digits']
                ]
            );

        /** @var \Magento\Eav\Model\Entity\Attribute $item */
        foreach ($collection->getItems() as $item) {
            $filteredAttributesOptions[] = [
                'value' => $item->getAttributeCode()
                    . SliderConfigInterface::SLIDER_FILTER_CONFIG_SEPARATOR
                    . $item->getId(),
                'label' => $item->getFrontendLabel() . (($item->getIsFilterable()) ? '' : ' (disabled)'),
            ];
        }

        return $filteredAttributesOptions;
    }

}
