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
 * @package   mirasvit/module-search-elastic
 * @version   1.2.45
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Plugin\Magento\CatalogSearch\Model\Indexer\Scope;

use Magento\CatalogSearch\Model\Indexer\Scope\State;

class StatePlugin
{
    public static $storedState = State::USE_REGULAR_INDEX;

    /**
     * Set the state to use temporary Index
     *
     * @return void
     */
    public function afterUseTemporaryIndex($subject, $response)
    {
        self::$storedState = State::USE_TEMPORARY_INDEX;
    }

    /**
     * Set the state to use regular Index
     *
     * @return void
     */
    public function afterUseRegularIndex($subject, $response)
    {
        self::$storedState = State::USE_REGULAR_INDEX;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function afterGetState($subject, $response)
    {
        return self::$storedState;
    }
}
