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
 * @package   mirasvit/module-search
 * @version   1.0.124
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Index\Magento\Catalog\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Mirasvit\Search\Model\Index\AbstractIndex;
use Mirasvit\Search\Model\Index\Context;

class Index extends AbstractIndex
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $collectionFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        CategoryCollectionFactory $collectionFactory,
        Context $context,
        $dataMappers
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $dataMappers);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Magento / Category';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'magento_catalog_category';
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [
            'name'             => __('Name'),
            'description'      => __('Description'),
            'meta_title'       => __('Page Title'),
            'meta_keywords'    => __('Meta Keywords'),
            'meta_description' => __('Meta Description'),
            'landing_page'     => __('CMS Block'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return 'entity_id';
    }

    /**
     * {@inheritdoc}
     */
    public function buildSearchCollection()
    {
        $collection = $this->collectionFactory->create()
            ->addNameToResult()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('level', ['gt' => 1]);

        if (strpos($collection->getSelect(), '`e`') !== false) {
            $this->context->getSearcher()->joinMatches($collection, 'e.entity_id');
        } else {
            $this->context->getSearcher()->joinMatches($collection, 'main_table.entity_id');
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableEntities($storeId, $entityIds = null, $lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->context->getStoreManager()->getStore($storeId);

        $root = $store->getRootCategoryId();

        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect(array_keys($this->getAttributes()))
            ->setStoreId($storeId)
            ->addPathsFilter("1/$root/")
            ->addFieldToFilter('is_active', 1);

        if ($entityIds) {
            $collection->addFieldToFilter('entity_id', ['in' => $entityIds]);
        }

        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId])
            ->setPageSize($limit)
            ->setOrder('entity_id');

        foreach ($collection as $item) {
            $item->setData('description', $this->prepareHtml($item->getData('description'), $storeId));
            $item->setData('landing_page', $this->renderCmsBlock($item->getData('landing_page'), $storeId));
        }

        return $collection;
    }

    /**
     * @param string $html
     * @param int    $storeId
     * @return string
     * @todo move to datamapper
     */
    protected function prepareHtml($html, $storeId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Email\Model\TemplateFactory $emailTemplateFactory */
        $emailTemplateFactory = $objectManager->create('Magento\Email\Model\TemplateFactory');

        /** @var \Magento\Cms\Model\Template\FilterProvider $filterProvider */
        $filterProvider = $objectManager->create('Magento\Cms\Model\Template\FilterProvider');

        try {
            /** @var \Magento\Store\Model\App\Emulation $emulation */
            $emulation = $objectManager->create('Magento\Store\Model\App\Emulation');
            $emulation->startEnvironmentEmulation($storeId, 'frontend', true);

            $template = $emailTemplateFactory->create();
            $template->emulateDesign($storeId);
            $template->setTemplateText($html)
                ->setIsPlain(false);
            $template->setTemplateFilter($filterProvider->getPageFilter());
            $html = $template->getProcessedTemplate([]);

            $emulation->stopEnvironmentEmulation();
        } catch (\Exception $e) {
        }

        return $html;
    }

    /**
     * @param int $blockId
     * @param int $storeId
     * @return string
     */
    protected function renderCmsBlock($blockId, $storeId)
    {
        if ($blockId == 0) {
            return '';
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        try {
            /** @var \Magento\Cms\Api\BlockRepositoryInterface $blockRepository */
            $blockRepository = $objectManager->get('Magento\Cms\Api\BlockRepositoryInterface');

            $block = $blockRepository->getById($blockId);

            return $this->prepareHtml($block->getContent(), $storeId);
        } catch (\Exception $e) {
        }

        return '';
    }
}
