define([
    'jquery',
    'Mirasvit_LayeredNavigation/js/constants'
], function($) {
    'use strict';

    function leftnavUpdate(leftnav) {
        var navigation = '.sidebar.sidebar-main .block.filter';

        if (leftnav) {
            $(navigation).first().replaceWith(leftnav);
            $(navigation).first().trigger('contentUpdated');
        }
    }

    function productsUpdate(products) {
        if (products) {
            $(window.mNavigationConstants.AJAX_PRODUCT_LIST_WRAPPER_ID).replaceWith(products);

            // trigger events
            $(window.mNavigationConstants.AJAX_PRODUCT_LIST_WRAPPER_ID).trigger('contentUpdated');

            setTimeout(function() {
                // execute after swatches are loaded
                $(window.mNavigationConstants.AJAX_PRODUCT_LIST_WRAPPER_ID).trigger('amscroll_refresh');
            }, 500);

            if ($.fn.lazyload) {
                // lazyload images for new content (Smartwave_Porto theme)
                $(window.mNavigationConstants.AJAX_PRODUCT_LIST_WRAPPER_ID + ' .porto-lazyload').lazyload({
                    effect: 'fadeIn'
                });
            }
        }
    }

    function additionalSidebarUpdate(selector, sidebar) {
        if (selector && selector != '' && sidebar) {
            $(selector).replaceWith(sidebar);
        }
    }

    function pageTitleUpdate(pageTitle) {
        $('#page-title-heading').closest('.page-title-wrapper').replaceWith(pageTitle);
        $('#page-title-heading').trigger('contentUpdated');
    }

    function breadcrumbsUpdate(breadcrumbs) {
        $('.breadcrumbs').replaceWith(breadcrumbs);
        $('.breadcrumbs').trigger('contentUpdated');
    }

    function updateCategoryViewData(categoryViewData) {
        if (categoryViewData != '') {
            if ($(".category-view").length == 0) {
                $('<div class="category-view"></div>').insertAfter('.page.messages');
            }

            $(".category-view").replaceWith(categoryViewData);
        }
    }

    function horizontalNavigationUpdate(horizontalNav, isHorizontalByDefault) {
        var horizontalNavigation = '.navigation-horizontal';

        if (horizontalNav) {
            if (isHorizontalByDefault == 1) {
                $("#layered-filter-block").remove();
            }

            $(horizontalNavigation).first().replaceWith(horizontalNav);
            $(horizontalNavigation).first().trigger('contentUpdated');
        }
    }

    function updateUrlPath(targetUrl) {
        var urlWithoutAjax = targetUrl.replace('?' + window.mNavigationConstants.AJAX_SUFFIX + '=1', '');

        if (urlWithoutAjax !== 'undefined') {
            urlWithoutAjax = targetUrl.replace('&' + window.mNavigationConstants.AJAX_SUFFIX + '=1', '');
        }

        // @todo %2B => +
        urlWithoutAjax.replace('&amp;', '&');
        urlWithoutAjax.replace('%2C', ',');

        window.mNavigationAjaxscrollCompatibility = 'true';
        window.location = urlWithoutAjax;
    }

    return function (data, isHorizontalByDefault) {
        if (data.ajaxscroll == 'true') {
            updateUrlPath(data.url);
        }

        leftnavUpdate(data.leftnav);
        horizontalNavigationUpdate(data.horizontalNavigation, isHorizontalByDefault);
        productsUpdate(data.products);
        additionalSidebarUpdate(data.sidebar_additional_selector, data.sidebar_additional);
        pageTitleUpdate(data.pageTitle);
        breadcrumbsUpdate(data.breadcrumbs);
        updateCategoryViewData(data.categoryViewData);
    };
});
