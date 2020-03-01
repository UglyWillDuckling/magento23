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



namespace Mirasvit\Brand\Plugin\Frontend\Magento\Catalog\Model\Layer\Category\FilterableAttributeList;

use Mirasvit\Brand\Api\Service\BrandActionServiceInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as ResourceEavAttribute;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;

class ExcludeCurrentBrandAttributePlugin
{
    /**
     * @var BrandActionServiceInterface
     */
    private $brandActionService;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(
        BrandActionServiceInterface $brandActionService,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        ConfigInterface $config
    ) {
        $this->brandActionService = $brandActionService;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Filter product collection
     *
     * @param FilterableAttributeList $subject
     * @param array|AttributeCollection $collection
     *
     * @return array|AttributeCollection
     */
    public function afterGetList(FilterableAttributeList $subject, $collection)
    {
        if ($this->brandActionService->isBrandViewPage()
            && ($brandAttribute = $this->config->getGeneralConfig()->getBrandAttribute())
        ) {
            $collection = $this->collectionFactory->create();
            $collection->setItemObjectClass(ResourceEavAttribute::class)
                ->addStoreLabel($this->storeManager->getStore()->getId())
                ->setOrder('position', 'ASC')
                ->addIsFilterableFilter()
                ->addFieldToFilter('attribute_code', ['neq' => $brandAttribute]);
            $collection->load();
        }

        return $collection;
    }
}
