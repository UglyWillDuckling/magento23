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



namespace Mirasvit\Sorting\Model\Config\Source;


use Mirasvit\Sorting\Model\Config;
use Mirasvit\Sorting\Api\Repository\CriterionRepositoryInterface;
use Mirasvit\Sorting\Api\Service\CriteriaManagementServiceInterface;

class Criteria implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CriterionRepositoryInterface
     */
    private $criterionRepository;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var CriteriaManagementServiceInterface
     */
    private $criteriaManagement;

    /**
     * @var array
     */
    private $criteriaCache = [];

    public function __construct(
        CriteriaManagementServiceInterface $criteriaManagement,
        CriterionRepositoryInterface $criterionRepository,
        Config $config
    ) {
        $this->criteriaManagement = $criteriaManagement;
        $this->criterionRepository = $criterionRepository;
        $this->config = $config;
    }

    /**
     * @return \Generator
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $code => $label) {
            $options[] = ['value' => $code, 'label' => $label];
        }

        return $options;
    }

    /**
     * @param bool  $isActive
     *
     * @return array
     */
    public function toArray($isActive = true)
    {
        if (0 === count($this->criteriaCache)) {
            $options = [];
            $defaultCriteria = $this->criteriaManagement->getDefaultCriteria();

            foreach ($defaultCriteria as $code => $label) {
                if (!$isActive || $this->criteriaManagement->isActive($code)) {
                    $options[$code] = $label;
                }
            }

            foreach ($this->criterionRepository->getList() as $criterion) {
                if (!$isActive || $this->criteriaManagement->isActive($criterion->getCode())) {
                    $options[$criterion->getCode()] = $criterion->getLabel();
                }
            }

            // if criteria not configured yet - use default sort by options
            if (!$options) {
                $options = $defaultCriteria;
            }

            $this->criteriaCache = $this->criteriaManagement->sortCriteria($options);
        }

        return $this->criteriaCache;
    }
}
