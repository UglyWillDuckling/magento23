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
 * @package   mirasvit/module-report
 * @version   1.3.76
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Repository;

use Mirasvit\Report\Api\Repository\WebApiRepositoryInterface;
use Mirasvit\ReportApi\Api\RequestInterface;

class WebApiRepository implements WebApiRepositoryInterface
{
    public function request(RequestInterface $request)
    {
        return $request->process();
    }
}