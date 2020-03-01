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


/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Product attribute add/edit form main tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Mirasvit\LayeredNavigation\Block\Adminhtml\ProductAttribute\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

use Magento\Eav\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Mirasvit\LayeredNavigation\Api\Repository\AttributeSettingsRepositoryInterface;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;

/**
 * @api
 * @since 100.0.2
 */
class Navigation extends Generic implements TabInterface
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param Yesno $yesNo
     * @param Data $eavData
     * @param array $disableScopeChangeList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        AttributeSettingsRepositoryInterface $attributeSettingsRepository,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->attribute = $registry->registry('entity_attribute');
        $this->attributeSettingsRepository = $attributeSettingsRepository;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Layered Navigation');
    }

    /**
     * Get tab title
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        $frontendInput = $this->attribute->getFrontendInput();
        if ($frontendInput == 'multiselect'
            || !$frontendInput ||  $frontendInput === 'price') {
                return true;
        }

        return false;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Adding product form elements for editing attribute
     *
     * @return $this
     * @SuppressWarnings(PHPMD)
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $attributeSettings = $this->getAttributeSettings();
        $form->setDataObject($attributeSettings);

        if (!$attributeSettings) {
            $form->addFieldset(
                'base_fieldset',
                [
                    'legend' => __('Multiselect attributes Layered Navigation fields will be available after attribute creation'),
                    'class' => 'fieldset-wide'
                ]
            );
            $this->setForm($form);

            return parent::_prepareForm();
        }

        $frontendInput = $this->attribute->getFrontendInput();

        $form->addField(
            AttributeSettingsInterface::ATTRIBUTE_CODE,
            'hidden',
            [
                'name' => AttributeSettingsInterface::ATTRIBUTE_CODE,
                'value' => ($attributeSettings->getAttributeCode()) ? : $this->attribute->getAttributeCode(),
            ]
        );

        $fieldset = $form->addFieldset('general_fieldset', [
            'legend' => __('General Properties'),
            'class' => 'fieldset-wide',
        ]);

        if ($frontendInput == 'price') {
            $fieldset->addField('is_slider', 'select', [
                'name' => 'is_slider',
                'label' => __('Is slider'),
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $attributeSettings->getIsSlider(),
            ]);
        }

        if ($frontendInput == 'multiselect') {
            $options = $dependence = $this->getLayout()->createBlock(
                'Mirasvit\LayeredNavigation\Block\Adminhtml\ProductAttribute\Images'
            );

            $this->setChild(
                'form_after',
                $options
            );
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getAttributeSettings()
    {
        if ($this->attribute->getId()) {
            $attributeCode = $this->attribute->getAttributeCode();
            $attributeSettings = $this->attributeSettingsRepository->getCollection()
                ->addFieldToFilter(AttributeSettingsInterface::ATTRIBUTE_CODE, $attributeCode)
                ->getFirstItem();

            return $attributeSettings;

        }

        return false;
    }

    public function getActive() {
        return true;
    }
}
