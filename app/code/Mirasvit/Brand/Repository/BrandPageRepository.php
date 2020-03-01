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



namespace Mirasvit\Brand\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterfaceFactory;
use Mirasvit\Brand\Model\ResourceModel\BrandPage\CollectionFactory;

class BrandPageRepository implements BrandPageRepositoryInterface
{
    /**
     * @var BrandPageInterfaceFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $brandPageByOptions = [];

    public function __construct(
        BrandPageInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @inheritdoc
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $model = $this->create();

        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : false;
    }

    /**
     * @inheritdoc
     */
    public function getByOptionId($optionId, $attributeId)
    {
        if (isset($this->brandPageByOptions[$attributeId][$optionId])) {
            return $this->brandPageByOptions[$attributeId][$optionId];
        }

        $collection = $this->getCollection();
        $collection->addFieldToFilter(BrandPageInterface::ATTRIBUTE_ID, $attributeId)
            ->addFieldToFilter(BrandPageInterface::ATTRIBUTE_OPTION_ID, $optionId);

        /** @var BrandPageInterface $brandPage */
        $brandPage = $collection->getFirstItem();
        if ($brandPage->getId()) {
            $this->brandPageByOptions[$attributeId][$optionId] = $brandPage;
        }

        return $brandPage;
    }

    /**
     * @inheritdoc
     */
    public function save(BrandPageInterface $brandPage)
    {
        return $this->entityManager->save($brandPage);
    }

    /**
     * @inheritdoc
     */
    public function delete(BrandPageInterface $brandPage)
    {
        return $this->entityManager->delete($brandPage);
    }
}