define(['jquery/ui'], function () {
    'use strict';
    
    //Don't use const. Const is not working for IE 8, 9 and 10
    window.mNavigationConstants = Object.freeze({
        "AJAX_SUFFIX":                    'mAjax',
        "AJAX_PRODUCT_LIST_WRAPPER_ID":   '#m-navigation-product-list-wrapper',
        "AJAX_STATE_WRAPPER_INPUT_CLASS": '.m-navigation-state-input',
        "AJAX_SWATCH_WRAPPER_CLASS":      '.m-navigation-swatch',
        
        "AJAX_SWATCH_HIGHLIGHT_CLASS": 'm-navigation-highlight-swatch',
        
        "AJAX_CALL":             'm__navigation_call_ajax',
        "MULTISELECT_SEPARATOR": ',',
        "CLEAR_ALL":             'clear_all'
    });
});
