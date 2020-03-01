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



namespace Mirasvit\LayeredNavigation\Model\ResourceModel\AttributeSettings;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\LayeredNavigation\Model\AttributeSettings::class,
            \Mirasvit\LayeredNavigation\Model\ResourceModel\AttributeSettings::class
        );

        $this->_idFieldName = AttributeSettingsInterface::ID;
    }
}
