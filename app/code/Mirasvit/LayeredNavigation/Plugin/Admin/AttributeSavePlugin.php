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



namespace Mirasvit\LayeredNavigation\Plugin\Admin;

use Mirasvit\LayeredNavigation\Api\Repository\AttributeSettingsRepositoryInterface;
use Mirasvit\LayeredNavigation\Api\Data\AttributeSettingsInterface;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;

class AttributeSavePlugin
{
    public function __construct(
        AttributeSettingsRepositoryInterface $attributeSettingsRepository,
        AttributeSettingsInterface $attributeSettings
    ) {
        $this->attributeSettingsRepository = $attributeSettingsRepository;
        $this->attributeSettings = $attributeSettings;
    }

    public function aroundSave($subject, \Closure $proceed)
    {
        $attributeCode = $subject->getData(AttributeSettingsInterface::ATTRIBUTE_CODE);
        if (!$attributeCode) {
            return $proceed();
        }
        $data = $subject->getData();
        $data[AttributeSettingsInterface::ATTRIBUTE_ID] =  $data['attribute_id'];

        if (isset($data[AttributeSettingsInterface::IMAGE_OPTIONS])) {
            $data[AttributeSettingsInterface::IMAGE_OPTIONS]
                = json_encode($data[AttributeSettingsInterface::IMAGE_OPTIONS]);
        }

        if (isset($data[AttributeSettingsInterface::FILTER_TEXT])) {
            $data[AttributeSettingsInterface::FILTER_TEXT]
                = json_encode($data[AttributeSettingsInterface::FILTER_TEXT]);
        }

        if (isset($data[AttributeSettingsInterface::IS_WHOLE_WIDTH_IMAGE])) {
            $data[AttributeSettingsInterface::IS_WHOLE_WIDTH_IMAGE]
                = json_encode($data[AttributeSettingsInterface::IS_WHOLE_WIDTH_IMAGE]);
        }

        $attributeSettings = $this->attributeSettingsRepository->getCollection()
            ->addFieldToFilter(AttributeSettingsInterface::ATTRIBUTE_CODE, $attributeCode)
            ->getFirstItem();
        $attributeSettings->addData($data);

        $this->attributeSettingsRepository->save($attributeSettings);

        return $proceed();
    }
}
