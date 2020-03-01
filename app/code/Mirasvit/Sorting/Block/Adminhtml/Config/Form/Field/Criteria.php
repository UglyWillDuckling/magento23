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
 * @package   mirasvit/module-sorting
 * @version   1.0.9
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Sorting\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mirasvit\Sorting\Api\Service\CriteriaManagementServiceInterface;
use Mirasvit\Sorting\Model\Config\Source\Criteria as CriteriaSource;

/**
 * @method AbstractElement getElement()
 * @method $this setElement(AbstractElement $element)
 */
class Criteria extends Field
{
    /**
     * @var CriteriaSource
     */
    private $criteriaSource;
    /**
     * @var CriteriaManagementServiceInterface
     */
    private $criteriaManagement;

    public function __construct(
        CriteriaManagementServiceInterface $criteriaManagement,
        CriteriaSource $criteriaSource,
        Context $context
    ) {
        $this->criteriaManagement = $criteriaManagement;
        $this->criteriaSource = $criteriaSource;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->setTemplate('Mirasvit_Sorting::config/form/field/criteria.phtml');
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }

    /**
     * Available criteria.
     *
     * @return array
     */
    public function getCriteria()
    {
        $options = [];
        $config = $this->getElement()->getData('value');

        foreach ($this->criteriaSource->toArray(false) as $code => $label) {
            $isActive = !$config && $this->criteriaManagement->isDefault($code)
                ? true
                : $this->getValue($code, 'is_active');

            $options[$code] = [
                'label'     => $label,
                'order'     => (int)$this->getValue($code, 'order'),
                'is_active' => $isActive,
                'default'   => false,
                'dir'       => $this->getValue($code, 'dir'),
            ];
        }

        if (isset($config['default'], $options[$config['default']])) {
            $options[$config['default']]['default'] = true;
        }

        return $options;
    }

    /**
     * Criterion name.
     *
     * @param string $id
     *
     * @return string
     */
    public function getNamePrefix($id)
    {
        return $this->getElement()->getName() . '[' . $id . ']';
    }

    /**
     * @param string $id
     * @param string $item
     *
     * @return string
     */
    private function getValue($id, $item)
    {
        if ($this->getElement()->getData('value') && is_array($this->getElement()->getData('value'))) {
            $values = $this->getElement()->getData('value');
            if (isset($values[$id][$item])) {
                return $values[$id][$item];
            }
        }

        return false;
    }
}
