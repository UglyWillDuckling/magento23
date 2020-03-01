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



namespace Mirasvit\SeoFilter\Api\Data;

interface RewriteInterface
{
    const TABLE_NAME = 'mst_seo_filter_rewrite';

    const ID = 'rewrite_id';
    const ATTRIBUTE_CODE = 'attribute_code';
    const OPTION_ID = 'option_id';
    const PRICE_OPTION_ID = 'price_option_id';
    const REWRITE = 'rewrite';
    const STORE_ID = 'store_id';

    const CATEGORY_ID = 'categoryId';
    const PARAMS = 'params';

    const PRICE = 'price';
    const FILTER_SEPARATOR = '-';
    const MULTISELECT_SEPARATOR = ',';

    /**
     * @return int
     */
    public function getId();

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
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $value
     * @return $this
     */
    public function setOptionId($value);

    /**
     * @return string
     */
    public function getPriceOptionId();

    /**
     * @param string $value
     * @return $this
     */
    public function setPriceOptionId($value);

    /**
     * @return string
     */
    public function getRewrite();

    /**
     * @param string $value
     * @return $this
     */
    public function setRewrite($value);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $value
     * @return $this
     */
    public function setStoreId($value);
}