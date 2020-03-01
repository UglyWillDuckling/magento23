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



namespace Mirasvit\Sorting\Plugin\Magento\Indexer\Model\Indexer;

use Magento\Framework\Mview\ConfigInterface;
use Magento\Framework\Mview\ViewInterfaceFactory;
use Magento\Framework\Mview\ViewInterface;
use Magento\Indexer\Model\Indexer;
use Mirasvit\Sorting\Model\Indexer as SortingIndexer;


class SwitchMviewState
{
    /**
     * @var ConfigInterface
     */
    private $mviewConfig;
    /**
     * @var ViewInterface
     */
    private $viewFactory;

    public function __construct(ConfigInterface $mviewConfig, ViewInterfaceFactory $viewFactory)
    {
        $this->mviewConfig = $mviewConfig;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Activate mview indexers for Smart Sorting criteria.
     *
     * @param Indexer $subject
     * @param bool    $scheduled
     */
    public function beforeSetScheduled(Indexer $subject, $scheduled)
    {
        if ($subject->getId() === SortingIndexer::INDEXER_ID) {
            foreach ($this->getCriteriaViews() as $view) {
                if ($scheduled) {
                    $view->subscribe();
                } else {
                    $view->unsubscribe();
                }
            }
        }
    }

    /**
     * Get mviews associated with the Smart Sorting criteria.
     *
     * @return \Generator|ViewInterface[]
     */
    private function getCriteriaViews()
    {
        foreach ($this->mviewConfig->getViews() as $viewId => $viewData) {
            if (strpos($viewId, SortingIndexer::INDEXER_ID . '_') !== false) {
                yield $this->viewFactory->create()->load($viewId);
            }
        }
    }
}
