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



namespace Mirasvit\LayeredNavigation\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;

class AttributeSettings extends AbstractModel implements AttributeSettingsInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mirasvit\LayeredNavigation\Model\ResourceModel\AttributeSettings::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeId($value)
    {
        return $this->setData(self::ATTRIBUTE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isSlider()
    {
        return $this->getData(self::IS_SLIDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSlider($value)
    {
        return $this->setData(self::IS_SLIDER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeCode($value)
    {
        return $this->setData(self::ATTRIBUTE_CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE_OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($value)
    {
        return $this->setData(self::IMAGE_OPTIONS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterText()
    {
        return $this->getData(self::FILTER_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterText($value)
    {
        return $this->setData(self::FILTER_TEXT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isWholeWidthImage()
    {
        return $this->getData(self::IS_WHOLE_WIDTH_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsWholeWidthImage($value)
    {
        return $this->setData(self::IS_WHOLE_WIDTH_IMAGE, $value);
    }
}