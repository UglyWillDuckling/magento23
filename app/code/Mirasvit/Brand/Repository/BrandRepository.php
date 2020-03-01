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

use Mirasvit\Brand\Api\Repository\BrandRepositoryInterface;
use Mirasvit\Brand\Api\Data\BrandInterfaceFactory;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection;
use Mirasvit\Brand\Api\Service\BrandAttributeServiceInterface;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * @var BrandInterfaceFactory
     */
    private $brandFactory;

    /**
     * @var Collection
     */
    private $collection = [];
    /**
     * @var BrandAttributeServiceInterface
     */
    private $brandAttributeService;

    public function __construct(
        BrandAttributeServiceInterface $brandAttributeService,
        BrandInterfaceFactory $brandFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->brandFactory = $brandFactory;
        $this->collectionFactory = $collectionFactory;
        $this->brandAttributeService = $brandAttributeService;
    }

    /**
     * @inheritdoc
     */
    public function create(array $data = [])
    {
        return $this->brandFactory->create($data);
    }

    /**
     * Returns only visible brands.
     *
     * @inheritdoc
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $collection = $this->collectionFactory->create();

            foreach ($this->brandAttributeService->getVisibleBrandOptions() as $option) {
                $brand = $this->create(['data' => $option]);
                $collection->addItem($brand);
            }

            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $items = $this->getCollection()->getItems();
        if (isset($items[$id])) {
            return $items[$id];
        }

        return $this->create();
    }
}
