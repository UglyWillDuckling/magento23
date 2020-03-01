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



namespace Mirasvit\LayeredNavigation\Block\Adminhtml\ProductAttribute;

use Magento\Framework\Registry;
use Mirasvit\LayeredNavigation\Api\Repository\AttributeSettingsRepositoryInterface;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;
use Magento\Backend\Model\UrlFactory;
use Mirasvit\LayeredNavigation\Model\ResourceModel\AttributeSettings\CollectionFactory;
use Mirasvit\LayeredNavigation\Api\Service\JsonServiceInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Block\Widget\Context;
use Magento\Eav\Model\Config;

class Images extends \Magento\Backend\Block\Widget
{
    public function __construct(
        UrlFactory $urlFactory,
        CollectionFactory $attributeSettingsCollectionFactory,
        JsonServiceInterface $json,
        FormFactory $formFactory,
        Context $context,
        Config $eavConfig,
        AttributeSettingsRepositoryInterface $attributeSettingsRepository,
        Registry $registry,
        array $data = []
    ) {
        $this->urlFactory = $urlFactory;
        $this->attributeSettingsCollectionFactory = $attributeSettingsCollectionFactory;
        $this->json = $json;
        $this->formFactory = $formFactory;
        $this->attribute = $registry->registry('entity_attribute');
        $this->eavConfig = $eavConfig;
        $this->attributeSettingsRepository = $attributeSettingsRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('attribute/images.phtml');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button')
            ->setData([
                'label' => __('Add New Row'),
                'id' => 'add_link_item',
                'class' => 'add',
            ]);

        return $addButton->toHtml();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getAttibuteOptions()
    {
        $attribute = $this->_getAttribute();
        $options = $attribute->getSource()->getAllOptions();
        $optionsPrepared = [];

        foreach ($options as $key => $option) {
            if (isset($option['value']) && $option['value']) {
                $optionsPrepared[$option['value']] = $options[$key];
            }
        }

        $attributeSettings = $this->attributeSettingsRepository->getCollection()
            ->addFieldToFilter(
                AttributeSettingsInterface::ATTRIBUTE_CODE,
                $this->attribute->getAttributeCode()
            )->getFirstItem();

        if ($attributeSettings && $attributeSettings->hasData()
            && ($imageOptions = $attributeSettings->getData(AttributeSettingsInterface::IMAGE_OPTIONS))) {
                $optionsData = json_decode($imageOptions, true);
                foreach ($optionsData as $key => $optionData) {
                    $optionResult = $optionData['display']['navigation_file'];
                    $optionResult = json_decode($optionResult, true);
                    if (isset($optionsPrepared[$key])) { //option can be deleted
                        $optionsPrepared[$key]['navigation_file_save'] = $optionResult[0];
                    }
                }
        }

        $attributeValues = $this->attributeSettingsCollectionFactory->create()
            ->getItemByColumnValue('mst_attribute_id', $attribute->getId());
        if ($attributeValues) {
            $mstFilterText = $attributeValues->getData('mst_filter_text');
            $mstFilterText = json_decode($mstFilterText, true);
            $mstIsWholeWidthImage = $attributeValues->getData('mst_is_whole_width_image');
            $mstIsWholeWidthImage = json_decode($mstIsWholeWidthImage, true);
        }
        foreach ($optionsPrepared as $optionPreparedKey => $optionPreparedValue) {
            if (isset($mstFilterText[$optionPreparedKey])) {
                $optionsPrepared[$optionPreparedKey]['mst_filter_text'] = $mstFilterText[$optionPreparedKey];
            }
            if (isset($mstIsWholeWidthImage[$optionPreparedKey])) {
                $optionsPrepared[$optionPreparedKey]['mst_is_whole_width_image']
                    = 'checked/';
            }
        }

        return $optionsPrepared;
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected function _getAttribute()
    {
        return $this->eavConfig->getAttribute('catalog_product', $this->attribute->getAttributeCode());
    }

    /**
     * @return string
     */
    public function getConfigJson()
    {
        $this->getConfig()->setUrl($this->urlFactory->create()
            ->addSessionParam()->getUrl('*/adminhtml_label/upload', ['_secure' => true]));
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        $this->getConfig()->setFileField('file');
        $this->getConfig()->setFilters([
            'all' => [
                'label' => __('All Files'),
                'files' => ['*.*'],
            ],
        ]);
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);

        return $this->json->unserialize($this->getConfig()->getData());
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getConfig()
    {
        if ($this->uploadConfig === null) {
            $this->uploadConfig = new \Magento\Framework\DataObject();
        }

        return $this->uploadConfig;
    }

    /**
     * @param string $fieldId
     * @param string $fieldName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageField($fieldId = 'img_field', $fieldName = 'img_field')
    {
        $form = $this->formFactory->create();
        $form->setFieldNameSuffix('label');

        $general = $form->addFieldset('fieldset_'.$fieldId, [
            'legend' => __('Image'),
            'html_id' => 'fieldsethtml_'.$fieldId,
        ]);
        $general->addType('image1', \Mirasvit\LayeredNavigation\Block\Adminhtml\ProductAttribute\ImageElement::class);
        $general->addField($fieldId, 'image1', [
            'label' => __('Title'),
            'required' => true,
            'name' => $fieldName,
            'value' => '',
            'html_id' => $fieldId,
        ]);

        return $general->getChildrenHtml();
    }
}

