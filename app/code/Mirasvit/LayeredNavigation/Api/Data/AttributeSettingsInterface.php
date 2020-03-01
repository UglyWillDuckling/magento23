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



namespace Mirasvit\LayeredNavigation\Api\Data;

interface AttributeSettingsInterface
{
    const TABLE_NAME = 'mst_ln_attribute_settings';

    const ID = 'mst_settings_id';
    const ATTRIBUTE_ID = 'mst_attribute_id';
    const IS_SLIDER = 'mst_is_slider';
    const ATTRIBUTE_CODE = 'mst_attribute_code';
    const IMAGE_OPTIONS = 'mst_image_options';
    const FILTER_TEXT = 'mst_filter_text';
    const IS_WHOLE_WIDTH_IMAGE = 'mst_is_whole_width_image';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $value
     * @return $this
     */
    public function setAttributeId($value);

    /**
     * @return int
     */
    public function isSlider();

    /**
     * @param int $value
     * @return $this
     */
    public function setIsSlider($value);

    /**
     * @return string
     */
    public function getAttributeCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setAttributeCode($value);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setImage($value);

    /**
     * @return string
     */
    public function getFilterText();

    /**
     * @param string $value
     * @return $this
     */
    public function setFilterText($value);

    /**
     * @return int
     */
    public function isWholeWidthImage();

    /**
     * @param int $value
     * @return $this
     */
    public function setIsWholeWidthImage($value);


}