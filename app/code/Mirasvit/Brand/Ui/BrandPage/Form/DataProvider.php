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



namespace Mirasvit\Brand\Ui\BrandPage\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Brand\Model\ResourceModel\BrandPage\CollectionFactory;
use Mirasvit\Brand\Api\Data\BrandPageStoreInterface;
use Mirasvit\Brand\Model\Image\ImageFile;
use Magento\Framework\App\Request\DataPersistorInterface;
use Mirasvit\Brand\Api\Service\ImageUrlServiceInterface;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ImageFile $imageFile,
        DataPersistorInterface $dataPersistor,
        ImageUrlServiceInterface $imageUrlService,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create()->addStoreColumn();
        $this->imageFile = $imageFile;
        $this->dataPersistor = $dataPersistor;
        $this->imageUrlService = $imageUrlService;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }


    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $storeIds = [];
        if ($data = $this->collection->getData()) {
            foreach ($data as $value) { //prepare store_id for multistore
                $storeIds[$value[BrandPageStoreInterface::BRAND_PAGE_ID]] = $value[BrandPageStoreInterface::STORE_ID];
            }
        }

        $result = [];

        foreach ($this->collection->getItems() as $item) {
            if (isset($storeIds[$item->getId()])) {  //prepare store_id for multistore
                $item->setData(BrandPageStoreInterface::STORE_ID, $storeIds[$item->getId()]);
            }

            $data = $item->getData();
            $data = $this->prepareImageData($data, 'logo');
            $data = $this->prepareImageData($data, 'banner');
            $result[$item->getId()] = $data;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareImageData($data, $imageKey)
    {
        if (isset($data[$imageKey])) {
            $imageName = $data[$imageKey];
            unset($data[$imageKey]);
            if ($this->imageFile->isExist($imageName)) {
                $stat = $this->imageFile ->getStat($imageName);
                $data[$imageKey] = [
                    [
                        'name' => $imageName,
                        'url' => $this->imageUrlService->getImageUrl($imageName),
                        'size' => isset($stat) ? $stat['size'] : 0,
                        'type' => $this->imageFile->getMimeType($imageName)
                    ]
                ];
            }
        }
        return $data;
    }
}
