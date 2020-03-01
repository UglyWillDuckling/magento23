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
 * @package   mirasvit/module-ajax-scroll
 * @version   1.0.7
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Scroll\Model\Config\Source;

class Mode implements \Magento\Framework\Data\OptionSourceInterface
{
    const MODE_INFINITE = 'infinite';
    const MODE_BUTTON   = 'button';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => self::MODE_INFINITE, 'label' => __('Infinite Scroll')],
            ['value' => self::MODE_BUTTON, 'label' => __('Load More Button')]
        ];
    }
}
