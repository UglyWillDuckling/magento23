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



namespace Mirasvit\SeoFilter\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;

class Rewrite extends AbstractModel implements RewriteInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mirasvit\SeoFilter\Model\ResourceModel\Rewrite::class);
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
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionId($value)
    {
        return $this->setData(self::OPTION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceOptionId()
    {
        return $this->getData(self::PRICE_OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceOptionId($value)
    {
        return $this->setData(self::PRICE_OPTION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRewrite() {
        return $this->getData(self::REWRITE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRewrite($value) {
        return $this->setData(self::REWRITE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId() {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($value) {
        return $this->setData(self::STORE_ID, $value);
    }

}