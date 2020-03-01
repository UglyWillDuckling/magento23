define([
    "jquery",
    "jquery/ui"
], function ($) {
    'use strict';

    //Work with checkbox
    $.widget('mst.mBrandFilter', {
        _create: function () {
            var self = this;
            var mBrandFilterConstants = Object.freeze({
                "LIST_ELEMENT_SELECTOR": '.brand-options .brand-row',
                "BRAND_PREFIX":   'brand-',
                "ALL_BRAND":   'letter-all-brand'
            });

            $(function () {
                var el = self.element;

                el.on('click', function () {
                    var selectedFilter = el.context.classList[0];
                    var selectedFilterCompare = mBrandFilterConstants.BRAND_PREFIX + selectedFilter;
                    var letterListElements = $(mBrandFilterConstants.LIST_ELEMENT_SELECTOR);

                    letterListElements.each( function() {
                        if (selectedFilterCompare !== "undefined"
                            && selectedFilter !== mBrandFilterConstants.ALL_BRAND
                            && selectedFilterCompare  !== $(this).context.classList[0]
                        ) {
                            $(this).addClass('hide');
                        } else {
                            $(this).removeClass('hide');
                        }
                    });
                });
            })
        }
    });

    return $.mst.mBrandFilter;
});
