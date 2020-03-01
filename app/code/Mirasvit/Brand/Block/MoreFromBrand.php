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


namespace Mirasvit\Brand\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Brand\Api\Repository\BrandRepositoryInterface;

class MoreFromBrand extends AbstractProduct
{
    const DEFAULT_PRODUCT_LIMIT = 6;
    const BRAND_NAME = '{brand_name}';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var ProductVisibility
     */
    private $productVisibility;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        BrandRepositoryInterface $brandRepository,
        Context $context,
        ConfigInterface $config,
        CollectionFactory $productCollectionFactory,
        ProductVisibility $productVisibility,
        ResourceConnection $resourceConnection,
        array $data = []
    ) {
        $this->registry = $context->getRegistry();
        $this->brandRepository = $brandRepository;
        $this->config = $config;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCollection()
    {
        return $this->productCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function _toHtml()
    {
        if ($this->config->getMoreFromBrandConfig()->isEnabled()
            && ($productCollection = $this->getProductCollection())
            && is_object($productCollection)
        ) {
            return parent::_toHtml();
        }

        return '';
    }


    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection();
        return parent::_beforeToHtml();
    }

    /**
     * {@inheritdoc}
     */
    protected function setProductCollection()
    {
        $product = $this->registry->registry('product');
        $brandAttributeCode = $this->getBrandAttribute();
        $brandAttributeOption = $product->getData($brandAttributeCode);
        if ($brandAttributeOption) {
            $limit = ($this->config->getMoreFromBrandConfig()->getProductsLimit())
                ? : self::DEFAULT_PRODUCT_LIMIT;
            if ($limit > 100) {
                $limit = 100;
            }
            $attributeOption = explode(',', $brandAttributeOption);
            $collection = $this->productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter($brandAttributeCode, ['in' => $attributeOption])
                ->addFieldToFilter('entity_id', ['neq' => $product->getId()])
                ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->setVisibility($this->productVisibility->getVisibleInSiteIds())
                ->addStoreFilter()
            ;

            $collection->getSelect()->joinLeft(
                ['inventory_table' => $this->resourceConnection->getTableName('cataloginventory_stock_item')],
                "inventory_table.product_id = e.entity_id", ['is_in_stock']);

            $collection->getSelect()->where('is_in_stock = ?', 1)
                ->orderRand()->limit($limit);

            //correct product urls in template
            foreach ($collection as $product) {
                $product->setDoNotUseCategoryId(true);
            }

            $this->productCollection = $collection;
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $title = $this->config->getMoreFromBrandConfig()->getTitle();
        if (strpos($title, self::BRAND_NAME) !== false) {
            $brandLabel = $this->getBrandLabel();
            $title = str_replace(self::BRAND_NAME, $brandLabel, $title);
        }

        return $title;
    }

    /**
     * @return string
     */
    private function getBrandLabel()
    {
        $brandAttributeOption = $this->getBrandAttributeOption();
        if (is_array($brandAttributeOption)) {
            $brandLabelArray = [];
            $brandAttributePreparedOptions = explode(',', $brandAttributeOption);
            foreach ($brandAttributePreparedOptions as $brandAttributePreparedOption) {
                $brandLabelArray[] = $this->brandRepository->get($brandAttributePreparedOption)->getLabel();
            }

            $brandLabel = implode(', ', $brandLabelArray);
        } else {
            $brandLabel = $this->brandRepository->get($brandAttributeOption)->getLabel();
        }

        return $brandLabel;
    }

    /**
     * @return string
     */
    private function getBrandAttributeOption()
    {
        $product = $this->registry->registry('product');
        $brandAttributeCode = $this->getBrandAttribute();

        return $product->getData($brandAttributeCode);
    }

    /**
     * @return string
     */
    private function getBrandAttribute()
    {
        return $this->config->getGeneralConfig()->getBrandAttribute();
    }
}
