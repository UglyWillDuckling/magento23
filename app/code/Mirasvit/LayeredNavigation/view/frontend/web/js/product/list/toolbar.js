define([
    'jquery',
    'jquery/ui',
    'Magento_Catalog/js/product/list/toolbar',
    'Mirasvit_LayeredNavigation/js/helper/url',
    'Mirasvit_LayeredNavigation/js/action/apply-filter'
], function ($, ___ui, mToolbar, url, applyFilter) {
    'use strict';
    
    /**
     * We rewrite this widget to enable AJAX for toolbar functionality.
     */
    $.widget('mst.productListToolbarForm', $.mage.productListToolbarForm, {
        blockToolbar: false,

        /**
         * @param {String} paramName
         * @param {*} paramValue
         * @param {*} defaultValue
         */
        changeUrl: function (paramName, paramValue, defaultValue) {
            return window.mNavigationConfigData
                ? this._changeAjaxUrl(paramName, paramValue, defaultValue)
                : this._changeStdUrl(paramName, paramValue, defaultValue);

        },

        /**
         * Change URL when AJAX enabled.
         *
         * @param {String} paramName
         * @param {*} paramValue
         * @param {*} defaultValue
         */
        _changeAjaxUrl: function(paramName, paramValue, defaultValue) {
            var link;

            if (this._isToolbarLock()) {
                return false;
            }

            window.mNavigationConfigData.cleanUrl = this._getToolbarCleanUrl(
                paramName,
                paramValue,
                defaultValue,
                this.options
            );

            //seo filter
            link = url.getLink(window.mNavigationConfigData.cleanUrl);

            applyFilter(link);
        },

        /**
         * Change URL without AJAX.
         *
         * @param {String} paramName
         * @param {*} paramValue
         * @param {*} defaultValue
         */
        _changeStdUrl: function (paramName, paramValue, defaultValue) {
            var decode    = window.decodeURIComponent,
                urlPaths  = this.options.url.split('?'),
                baseUrl   = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters, i;

            for (i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined
                    ? url.decode(parameters[1])
                    : '';
            }

            paramData[paramName] = paramValue;

            if (paramValue == defaultValue) { //eslint-disable-line eqeqeq
                delete paramData[paramName];
            }

            paramData = $.param(paramData);

            //fix incorrect symbols in url
            paramData = paramData.replace(/%2C/g, ",");

            location.href = baseUrl + (paramData.length ? '?' + paramData : '');
        },

        _getToolbarCleanUrl: function(paramName, paramValue, defaultValue, options) {
            var decode = window.decodeURIComponent,
                urlPaths  = options.url.split('?'),
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters, i;

            for (i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined
                    //? decode(parameters[1].replace(/\+/g, '%20'))
                    ? parameters[1]
                    : '';
            }
            paramData[paramName] = paramValue;

            if (paramValue == defaultValue) {
                delete paramData[paramName];
            }

            return url.prepareUrl(paramData, paramName, paramData[paramName] ? paramData[paramName] : '');
        },

        _isToolbarLock: function () {
            var self = this;

            if (this.blockToolbar) {
                return true;
            }

            this.blockToolbar = true;
            setTimeout(function () {
                self.blockToolbar = false;
            }, 300);

            return false;
        }
    });
    
    return $.mst.productListToolbarForm;
});
