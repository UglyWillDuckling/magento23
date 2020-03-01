define([
    'jquery',
    'Mirasvit_LayeredNavigation/js/action/apply-filter',
    'Mirasvit_LayeredNavigation/js/constants'
], function($, applyFilter) {
    'use strict';

    /**
     * Work with swatch filters.
     */
    $.widget('mst.mNavigationSwatch', {
        _create: function () {
            this._bind();
        },

        _bind: function () {
            var swatchSelector = '[name="' + $(this.element[0])
                    .find(window.mNavigationConstants.AJAX_SWATCH_WRAPPER_CLASS)
                    .attr('m-navigation-filter') + '"]';

            $(this.element[0]).find('a').on('click', function (e) {
                var link = $(this),
                    input = link.find(swatchSelector),
                    href = link.attr('href'),
                    isChecked = input.prop('className'),
                    filterValue, filterName, filterData;

                input.prop('checked', !input.prop('checked'));
                if (isChecked.indexOf(window.mNavigationConstants.AJAX_SWATCH_HIGHLIGHT_CLASS) != '-1') {
                    filterValue = input.prop('value');
                    filterName = input.prop('name');
                    filterData = {filterValue: filterValue, filterName: filterName};

                    applyFilter(href, false, false, filterData);
                } else {
                    applyFilter(href);
                }

                e.stopPropagation();
                e.preventDefault();
            });
        }
    });

    return $.mst.mNavigationSwatch;
});
