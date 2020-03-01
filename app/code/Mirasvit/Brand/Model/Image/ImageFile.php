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

use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class ImageFile
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Mime
     */
    private $mime;

    public function __construct(
        ImageUploader $imageUploader,
        Filesystem $filesystem,
        Mime $mime
    ) {
        $this->imageUploader = $imageUploader;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mime = $mime;
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function getMimeType($fileName)
    {
        $absoluteFilePath = $this->mediaDirectory->getAbsolutePath(
            $this->getFilePath($fileName)
        );
        return $this->mime->getMimeType($absoluteFilePath);
    }

    /**
     * @param string $fileName
     * @return array
     */
    public function getStat($fileName)
    {
        return $this->mediaDirectory->stat($this->getFilePath($fileName));
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function isExist($fileName)
    {
        return $this->mediaDirectory->isExist($this->getFilePath($fileName));
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getFilePath($fileName)
    {
        return $this->imageUploader->getFilePath(
            $this->imageUploader->getBasePath(),
            $fileName
        );
    }
}
