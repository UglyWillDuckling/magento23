define([
    'jquery'
], function($) {
    'use strict';

    function scrollToHeader() {
        var headerHeight = $('.page-header:first').height();

        $('html, body').animate({
            scrollTop: headerHeight
        }, 500, 'easeOutExpo');

        //$(window).scrollTop(headerHeight, 'easing');
        //    'scrollTop': headerHeight
        //}, 900, 'swing');
    }

    /**
     * Component implements overlay logic for layered navigation.
     */
    return {
        overlayClass: 'navigation-overlay',

        show: function() {
            scrollToHeader();

            $('.columns').append($('<div><i class="fa fa-spinner fa-spin"></i></div>').addClass(this.overlayClass));
        },

        hide: function () {
            $('.' + this.overlayClass).remove();
        }
    };
});
