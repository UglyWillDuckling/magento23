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



namespace Mirasvit\LayeredNavigation\Api\Repository;

use Magento\Framework\DataObject;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;

interface AttributeSettingsRepositoryInterface
{
    /**
     * @return AttributeSettingsInterface[]|\Mirasvit\LayeredNavigation\Model\ResourceModel\AttributeSettings\Collection
     */
    public function getCollection();

    /**
     * @return AttributeSettingsInterface
     */
    public function create();

    /**
     * @param int $id
     * @return AttributeSettingsInterface|DataObject|false
     */
    public function get($id);

    /**
     * @param AttributeSettingsInterface $model
     * @return AttributeSettingsInterface
     */
    public function save(AttributeSettingsInterface $model);

    /**
     * @param AttributeSettingsInterface $model
     * @return bool
     */
    public function delete(AttributeSettingsInterface $model);
}