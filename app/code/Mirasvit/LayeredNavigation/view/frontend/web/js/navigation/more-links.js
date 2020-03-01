define([
    "jquery",
    "domReady!"
], function ($) {
    'use strict';

    $.fn.mToggleClick = function() {
        var functions = arguments,
            iteration = 0;

        return this.click(function() {
            functions[iteration].call();
            iteration = (iteration + 1) % functions.length;
        });
    };

    $.widget('mst.mNavigationMoreLinks', {
        _create: function () {
            this._bind();
        },

        _bind: function () {
            var moreText = this.options.moreText,
                lessText = this.options.lessText,
                more = this.options.classMore,
                moreLinks = 'more-links-' + this.options.classMore;

            $("." + moreLinks).mToggleClick(function () {
                this.element.text(lessText);
                $("." + more).show();
            }.bind(this), function () {
                this.element.text(moreText);
                $("." + more).hide();
            }.bind(this));
        }
    });

    return $.mst.mNavigationMoreLinks;
});
