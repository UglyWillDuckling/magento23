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



namespace Mirasvit\Brand\Model\Image;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class ThumbnailFile
{
    /**
     * Base path to brand logo images
     */
    const IMAGE_PATH = 'brand/brand';

    /**
     * @var ImageFactory
     */
    private $imageProcessorFactory;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AssetRepository
     */
    private $assetRepo;

    /**
     * @var array
     */
    private $imageTypes = [];

    /**
     * @param ImageFactory $imageProcessorFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param AssetRepository $assetRepo
     * @param array $imageTypes
     */
    public function __construct(
        ImageFactory $imageProcessorFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        AssetRepository $assetRepo,
        $imageTypes = []
    ) {
        $this->imageProcessorFactory = $imageProcessorFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->imageTypes = $imageTypes;
        $this->assetRepo = $assetRepo;
    }

    /**
     * @param string $imageType
     * @param string $fileName
     * @return bool
     */
    public function hasImage($imageType, $fileName)
    {
        return $this->mediaDirectory->isExist($this->getImagePath($imageType, $fileName));
    }

    /**
     * @param string $imageType
     * @param string $fileName
     * @throws \Exception
     * @return void
     */
    public function createImage($imageType, $fileName)
    {
        $this->copyAndResize(
            $fileName,
            $this->getImagePath($imageType, $fileName),
            $this->getImageTypeData($imageType, 'imageSize')
        );
    }

    /**
     * @param string $imageType
     * @param string $fileName
     * @return string
     */
    public function getImageUrl($imageType, $fileName)
    {
        /** @var StoreInterface|Store $store */
        $store = $this->storeManager->getStore();
        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . $this->getImagePath($imageType, $fileName);
    }

    /**
     * @param string $imageType
     * @return string
     * @throws \Exception
     */
    public function getImagePlaceholderUrl($imageType)
    {
        return $this->assetRepo->getUrl($this->getImageTypeData($imageType, 'placeholderPath'));
    }

    /**
     * @param string $imageType
     * @param string $fileName
     * @return string
     * @throws \Exception
     */
    private function getImagePath($imageType, $fileName)
    {
        return $this->getFilePath($this->getImageTypeData($imageType, 'path'), $fileName);
    }

    /**
     * @param string $imageType
     * @param string $fieldName
     * @return mixed
     * @throws \Exception
     */
    private function getImageTypeData($imageType, $fieldName)
    {
        if (!isset($this->imageTypes[$imageType])) {
            throw new \Exception(
                'Unknown image type: ' . $imageType
            );
        }
        return $this->imageTypes[$imageType][$fieldName];
    }

    /**
     * @param string $fileName
     * @param string $path
     * @param int $size
     * @return void
     */
    private function copyAndResize($fileName, $path, $size)
    {
        $this->mediaDirectory->copyFile(
            $this->getFilePath(self::IMAGE_PATH, $fileName),
            $path
        );
        $filePath = $this->mediaDirectory->getAbsolutePath($path);

        $imageProcessor = $this->imageProcessorFactory->create($filePath);
        $imageProcessor->keepAspectRatio(true);
        $imageProcessor->keepFrame(true);
        $imageProcessor->keepTransparency(true);
        $imageProcessor->backgroundColor([255, 255, 255]);
        $imageProcessor->constrainOnly(true);
        $imageProcessor->quality(80);
        $imageProcessor->resize($size);
        $imageProcessor->save();
    }

    /**
     * @param string $path
     * @param string $fileName
     * @return string
     */
    private function getFilePath($path, $fileName)
    {
        return rtrim($path, '/') . '/' . ltrim($fileName, '/');
    }
}
