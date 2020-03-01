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



namespace Mirasvit\Sorting\Service;


use Magento\Catalog\Model\Config as CatalogConfig;
use Mirasvit\Sorting\Api\Service\CriteriaManagementServiceInterface;
use Mirasvit\Sorting\Model\Config;

class CriteriaManagementService implements CriteriaManagementServiceInterface
{
    const DEFAULT_DIRECTION = 'asc';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $sortByAttributes;
    /**
     * @var CatalogConfig
     */
    private $catalogConfig;

    public function __construct(CatalogConfig $catalogConfig, Config $config)
    {
        $this->config = $config;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultCriterion()
    {
        $config = $this->config->getCriteriaConfig();

        return (isset($config['default'])) ? $config['default'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getDirection($criterionCode)
    {
        $config = $this->config->getCriteriaConfig();

        return isset($config[$criterionCode]['dir']) ? $config[$criterionCode]['dir'] : self::DEFAULT_DIRECTION;
    }

    /**
     * @inheritdoc
     */
    public function isActive($criterionCode)
    {
        $config = $this->config->getCriteriaConfig();

        return isset($config[$criterionCode]['is_active']) && $config[$criterionCode]['is_active'];
    }

    /**
     * @inheritdoc
     */
    public function isDefault($criterionCode)
    {
        return array_key_exists($criterionCode, $this->getDefaultCriteria());
    }

    /**
     * @inheritdoc
     */
    public function sortCriteria(array $criteria = [])
    {
        $config = $this->config->getCriteriaConfig();

        if ($config && is_array($config)) {
            uksort($criteria, function ($a, $b) use ($config) {
                $orderA = (int) isset($config[$a]) ? $config[$a]['order'] : 0;
                $orderB = (int) isset($config[$b]) ? $config[$b]['order'] : 0;

                return $orderA > $orderB ? 1 : -1;
            });
        }

        return $criteria;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultCriteria()
    {
        if (!$this->sortByAttributes) {
            $options = ['position' => __('Position')];
            foreach ($this->catalogConfig->getAttributesUsedForSortBy() as $attribute) {
                /* @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
                $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
            }

            $this->sortByAttributes = $options;
        }

        return $this->sortByAttributes;
    }
}
