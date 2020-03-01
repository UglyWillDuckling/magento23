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



namespace Mirasvit\SeoNavigation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MetaRobots implements ArrayInterface
{
    const NOINDEX_NOFOLLOW = 1;
    const NOINDEX_FOLLOW   = 2;
    const INDEX_NOFOLLOW   = 3;
    const INDEX_FOLLOW     = 4;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Don\'t change')],
            ['value' => self::NOINDEX_NOFOLLOW, 'label' => 'NOINDEX, NOFOLLOW'],
            ['value' => self::NOINDEX_FOLLOW, 'label' => 'NOINDEX, FOLLOW'],
            ['value' => self::INDEX_NOFOLLOW, 'label' => 'INDEX, NOFOLLOW'],
            ['value' => self::INDEX_FOLLOW, 'label' => 'INDEX, FOLLOW'],
        ];
    }
}
