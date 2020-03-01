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



namespace Mirasvit\SeoFilter\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\SeoFilter\Api\Data\PriceRewriteInterface;
use Mirasvit\SeoFilter\Api\Repository\PriceRewriteRepositoryInterface;
use Mirasvit\SeoFilter\Api\Data\PriceRewriteInterfaceFactory;
use Mirasvit\SeoFilter\Model\ResourceModel\PriceRewrite\CollectionFactory;

class PriceRewriteRepository implements PriceRewriteRepositoryInterface
{
    /**
     * @var PriceRewriteInterfaceFactory
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

    public function __construct(
        PriceRewriteInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $model = $this->create();

        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : false;
    }

    /**
     * {@inheritdoc}
     */
    public function save(PriceRewriteInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PriceRewriteInterface $model)
    {
        return $this->entityManager->delete($model);
    }
}