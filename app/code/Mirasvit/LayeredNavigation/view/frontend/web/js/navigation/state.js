define([
    'jquery',
    'Mirasvit_LayeredNavigation/js/action/apply-filter',
    'Mirasvit_LayeredNavigation/js/constants'
], function($, applyFilter) {
    'use strict';

    /**
     * Widget resets applied filters individually or completely.
     */
    $.widget('mst.mNavigationState', {
        options: {
            /**
             * Element used to reset filters.
             *
             * @type {string}
             */
            clearAllSelector: 'a.filter-clear',

            /**
             * Wrapper of individual filters.
             *
             * @type {string|null}
             */
            filtersWrapperSelector: null,

            /**
             * Url used to clean all filters.
             *
             * @type {String|null}
             */
            cleanAllUrl: null
        },

        _create: function () {
            this._bind();
        },

        _bind: function() {
            var self = this,
                el, stateLinks;

            // bind event to clear all filters
            $(this.options.clearAllSelector).on('click', function (e) {
                var link = $(this).prop('href');

                window.mNavigationFriendlySeoUrl = link; //seo filter

                applyFilter(link, false, self.options.cleanAllUrl, {}, false, true);

                e.stopPropagation();
                e.preventDefault();
            });

            // bind events to remove individual filters
            $(function () {
                el = $(self.element[0]);
                stateLinks = el.find(self.options.filtersWrapperSelector);

                if (stateLinks.length) {
                    $(stateLinks).each(function (index, element) {
                        var filterAttribute = $(element).data('container');
                        var filterValue = $(element).data('value');

                        $(element).find('a').on("click", function (e) {
                            self._removeFilter(filterAttribute, filterValue);
                            e.stopPropagation();
                            e.preventDefault();
                        });

                    });
                }
            });
        },

        _removeFilter: function (attribute, value) {
            var filterData = {filterValue: value, filterName: attribute},
                link = $('li[data-container="' + attribute + '"][data-value="' + value + '"] a.remove').attr('href');

            try {
                $('li[data-container="' + attribute + '"][data-value="' + value + '"]').remove();
                $('input[name="' + attribute + '"][value="' + value + '"]').remove();

                if (!this.element.find('li').length) {
                    this.element.remove(); // ??? unknown purpose
                    $('.filter-actions').remove();
                }

                applyFilter(link, false, false, filterData);
            } catch (e) {
                console.log(e);
                link.replace('&amp;', '&');
                link.replace('%2C', ',');
                window.location = link;
            }
        }
    });

    return $.mst.mNavigationState;
});
