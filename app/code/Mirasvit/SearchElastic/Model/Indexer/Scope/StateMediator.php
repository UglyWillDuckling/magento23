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



namespace Mirasvit\SearchElastic\Model\Indexer\Scope;

use Mirasvit\Core\Service\CompatibilityService;

if (CompatibilityService::is22() || CompatibilityService::is23()) {
    require_once('StateMediatorExtends.php');
} else {
    require_once('StateMediatorSimple.php');
}

class StateMediator extends StateMediatorParent
{
	const USE_TEMPORARY_INDEX = 'use_temporary_table';
    const USE_REGULAR_INDEX = 'use_main_table';
}
