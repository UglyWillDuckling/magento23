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



namespace Mirasvit\Sorting\Repository;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Sorting\Api\Repository\CriterionRepositoryInterface;

class CriterionRepository implements CriterionRepositoryInterface
{
    /**
     * @var array
     */
    private $criteria;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager, array $criteria = [])
    {
        $this->criteria = $criteria;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function get($code)
    {
        if (!isset($this->criteria[$code])) {
            throw new LocalizedException(__('Criterion "%1" does not exists.', $code));
        }

        return $this->objectManager->get($this->criteria[$code]);
    }

    /**
     * @inheritdoc
     */
    public function has($code)
    {
        return isset($this->criteria[$code]);
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        $criteria = [];

        foreach ($this->criteria as $code => $class) {
            $criteria[$code] = $this->get($code);
        }

        return $criteria;
    }
}
