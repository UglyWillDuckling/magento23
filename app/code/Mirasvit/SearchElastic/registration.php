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


if (isset($_SERVER)
    && is_array($_SERVER)
    && isset($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], 'searchautocomplete/ajax/suggest') !== false
) {
    $configFile = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/app/etc/autocomplete.json';
    if (!@file_exists($configFile)) { //file_exists may not work if open_basedir restriction in effect..
        //module is installed in app/code/Mirasvit
        $configFile = dirname(dirname(dirname(dirname(__DIR__)))) . '/app/etc/autocomplete.json';
    }
    if (@file_exists($configFile)) {
        require_once 'autocomplete.php';
    }
}


$registration = dirname(dirname(dirname(__DIR__))) . '/vendor/mirasvit/module-search-elastic/src/SearchElastic/registration.php';
if (@file_exists($registration)) {//file_exists may not work if open_basedir restriction in effect. 
    # module was already installed via composer
    return;
}

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Mirasvit_SearchElastic',
    __DIR__
);
