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



namespace Mirasvit\LayeredNavigation\Api\Config;

interface MultiselectDisplayOptionsInterface
{
    const OPTION_DEFAULT = '0';
    const OPTION_SIMPLE_CHECKBOX = '1';
    const OPTION_CHECKBOX = 'checkbox';
    const OPTION_CHECKBOX_BLUE = 'checkbox checkbox-primary';
    const OPTION_CHECKBOX_GREEN = 'checkbox checkbox-success';
    const OPTION_CHECKBOX_LIGHT_BLUE = 'checkbox checkbox-info';
    const OPTION_CHECKBOX_YELLOW = 'checkbox checkbox-warning';
    const OPTION_CHECKBOX_RED = 'checkbox checkbox-danger';
    const OPTION_CIRCLE = 'checkbox checkbox-circle';
    const OPTION_CIRCLE_BLUE = 'checkbox checkbox-primary checkbox-circle';
    const OPTION_CIRCLE_GREEN = 'checkbox checkbox-success checkbox-circle';
    const OPTION_CIRCLE_LIGHT_BLUE = 'checkbox checkbox-info checkbox-circle';
    const OPTION_CIRCLE_YELLOW = 'checkbox checkbox-warning checkbox-circle';
    const OPTION_CIRCLE_RED = 'checkbox checkbox-danger checkbox-circle';
}