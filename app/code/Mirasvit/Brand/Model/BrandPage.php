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



namespace Mirasvit\Brand\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Model\Image\ImageFile;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Catalog\Model\ImageUploader;

class BrandPage extends AbstractModel implements BrandPageInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        ImageFile $imageFile,
        ImageUploader $imageUploader,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->imageFile = $imageFile;
        $this->imageUploader = $imageUploader;
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mirasvit\Brand\Model\ResourceModel\BrandPage::class);
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
    public function getAttributeOptionId()
    {
        return $this->getData(self::ATTRIBUTE_OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeOptionId($value)
    {
        return $this->setData(self::ATTRIBUTE_OPTION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeId($value)
    {
        return $this->setData(self::ATTRIBUTE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogo()
    {
        return $this->getData(self::LOGO);
    }

    /**
     * {@inheritdoc}
     */
    public function setLogo($value)
    {
        return $this->setData(self::LOGO, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandTitle()
    {
        return $this->getData(self::BRAND_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrandTitle($value)
    {
        return $this->setData(self::BRAND_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($value)
    {
        return $this->setData(self::URL_KEY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandDescription()
    {
        return $this->getData(self::BRAND_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrandDescription($value)
    {
        return $this->setData(self::BRAND_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($value)
    {
        return $this->setData(self::META_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyword()
    {
        return $this->getData(self::KEYWORD);
    }

    /**
     * {@inheritdoc}
     */
    public function setKeyword($value)
    {
        return $this->setData(self::KEYWORD, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($value)
    {
        return $this->setData(self::META_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRobots()
    {
        return $this->getData(self::ROBOTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setRobots($value)
    {
        return $this->setData(self::ROBOTS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonical()
    {
        return $this->getData(self::CANONICAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCanonical($value)
    {
        return $this->setData(self::CANONICAL, $value);
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
    public function getBrandName()
    {
        return $this->getData(self::BRAND_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrandName($value)
    {
        return $this->setData(self::BRAND_NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerAlt()
    {
        return $this->getData(self::BANNER_ALT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBannerAlt($value)
    {
        return $this->setData(self::BANNER_ALT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerTitle()
    {
        return $this->getData(self::BANNER_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBannerTitle($value)
    {
        return $this->setData(self::BANNER_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBanner()
    {
        return $this->getData(self::BANNER);
    }

    /**
     * {@inheritdoc}
     */
    public function setBanner($value)
    {
        return $this->setData(self::BANNER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerPosition()
    {
        return $this->getData(self::BANNER_POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setBannerPosition($value)
    {
        return $this->setData(self::BANNER_POSITION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandShortDescription()
    {
        return $this->getData(self::BRAND_SHORT_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrandShortDescription($value)
    {
        return $this->setData(self::BRAND_SHORT_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $logo = $this->getLogo();
        $this->moveFileFromTmp($logo);
        $banner = $this->getBanner();
        $this->moveFileFromTmp($banner);

        return parent::afterSave();
    }

    /**
     * @param string $image
     * @return void
     */
    private function moveFileFromTmp($image)
    {
        if ($image
            && !$this->imageFile->isExist($image)) {
                $this->imageUploader->moveFileFromTmp($image);
        }
    }
}