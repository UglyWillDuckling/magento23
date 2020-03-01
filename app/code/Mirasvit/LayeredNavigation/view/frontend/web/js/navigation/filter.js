define([
    'jquery',
    'Mirasvit_LayeredNavigation/js/action/apply-filter',
    'Mirasvit_LayeredNavigation/js/constants'
], function($, applyFilter) {
    'use strict';

    /**
     * Work with default filters.
     */
    $.widget('mst.mNavigationFilter', {
        _create: function () {
            if (this.options.isAjaxEnabled == 1) {
                this._bind();
            }
        },

        _bind: function () {
            this.element.on('click', function (e) {
                var href = this.element.prop('href');
                var checkbox = this.element.find('input[type=checkbox]', '#layered-filter-block a');
                var isChecked = checkbox.prop('checked'),
                    filterValue, filterName, filterData;

                if (window.mNavigationFilterCheckboxAjaxApplied === undefined) {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }

                if (window.mNavigationIsSimpleCheckboxChecked !== undefined) {
                    isChecked = window.mNavigationIsSimpleCheckboxChecked;
                }

                if (isChecked) {
                    filterValue = checkbox.prop('value');
                    filterName = checkbox.prop('name');
                    filterData = {filterValue: filterValue, filterName: filterName};

                    applyFilter(href, false, false, filterData);
                } else {
                    applyFilter(href);
                }

                e.stopPropagation();
                if (window.mNavigationFilterCheckboxAjaxApplied === undefined) {
                    e.preventDefault();
                }

                window.mNavigationFilterCheckboxAjaxApplied = undefined;
            }.bind(this));
        }
    });

    return $.mst.mNavigationFilter;
});
