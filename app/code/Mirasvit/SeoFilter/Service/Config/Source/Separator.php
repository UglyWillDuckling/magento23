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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Service\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface;

class Separator implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => ConfigInterface::FILTER_NAME_WITHOUT_SEPARATOR, 'label' => __('Do not use a separator')],
            ['value' => ConfigInterface::FILTER_NAME_BOTTOM_DASH_SEPARATOR, 'label' => __('Use "_" as a separator')],
            ['value' => ConfigInterface::FILTER_NAME_CAPITAL_LETTER_SEPARATOR, 'label' => __('Use capital letter as a separator')],
        ];

        return $options;
    }
}
