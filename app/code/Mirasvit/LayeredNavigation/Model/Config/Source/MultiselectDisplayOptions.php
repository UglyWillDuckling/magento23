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
use Mirasvit\LayeredNavigation\Api\Config\MultiselectDisplayOptionsInterface;

class MultiselectDisplayOptions implements ArrayInterface, MultiselectDisplayOptionsInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => MultiselectDisplayOptionsInterface::OPTION_DEFAULT, 'label' => __('Default')],
            ['value' => MultiselectDisplayOptionsInterface::OPTION_SIMPLE_CHECKBOX, 'label' => __('Simple Checkbox')],
            ['value' => MultiselectDisplayOptionsInterface::OPTION_CHECKBOX, 'label' => __('Checkbox')],
            ['value' => MultiselectDisplayOptionsInterface::OPTION_CIRCLE, 'label' => __('Circle')],
        ];

        return $options;
    }

}
