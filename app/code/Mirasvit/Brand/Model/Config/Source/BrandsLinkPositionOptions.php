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



namespace Mirasvit\Brand\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class BrandsLinkPositionOptions implements ArrayInterface
{
    CONST TOP_MENU_FIRST = 1;
    CONST TOP_MENU_LAST = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => self::TOP_MENU_FIRST, 'label' => __('Top menu ( first )')],
            ['value' => self::TOP_MENU_LAST, 'label' => __('Top menu ( last )')],
//            ['value' => 3, 'label' => __('Top Links')],
//            ['value' => 4, 'label' => __('Any template ')],
        ];

        return $options;
    }
}
