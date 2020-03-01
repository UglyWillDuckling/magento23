define([
    "jquery",
    "jquery/ui"
], function ($) {
    'use strict';

    //Work with checkbox
    $.widget('mst.mNavigationFilterLink', {
        _create: function () {
            this._bind();
        },

        _bind: function () {
            var el = this.element;

            if (this.options.isAjaxEnabled == 0) {
                el.on('click', function () {
                    var checkbox = el.find('input[type=checkbox]');

                    if (checkbox.prop('checked') && window.mNavigationFilterCheckboxApplied != true) {
                        checkbox.context.checked = false;
                        checkbox.prop('checked', !checkbox.prop('checked'));
                    } else if (!checkbox.prop('checked') && window.mNavigationFilterCheckboxApplied != true) {
                        checkbox.context.checked = true;
                        checkbox.prop('checked', 'checked');
                    }
                });
            }

            if (this.options.isAjaxEnabled == 1) {
                el.on('click', function () {
                    if (this.options.isStylizedCheckbox == 0) {
                        window.mNavigationIsSimpleCheckboxChecked = undefined;
                    }
                }.bind(this));
            }
        }
    });

    return $.mst.mNavigationFilterLink;
});
