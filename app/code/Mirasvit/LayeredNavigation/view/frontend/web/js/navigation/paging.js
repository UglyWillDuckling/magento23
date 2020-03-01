define([
    'jquery',
    'Mirasvit_LayeredNavigation/js/action/apply-filter',
    'Mirasvit_LayeredNavigation/js/helper/url',
    'Mirasvit_LayeredNavigation/js/constants'
], function($, applyFilter, url) {
    'use strict';

    /**
     * Init AJAX paging.
     */
    return function () {
        //change page number
        $(".toolbar .pages a").on('click', function (e) {
            var newUrl = $(this).prop('href'),
                updatedUrl = null,
                urlPaths  = newUrl.split('?'),
                urlParams = urlPaths[1].split('&'),
                link, pageParam, i;

            for (i = 0; i < urlParams.length; i++) {
                if (urlParams[i].indexOf("p=") === 0) {
                    pageParam = urlParams[i].split('=');
                    updatedUrl = url.prepareUrl(
                        urlParams,
                        pageParam[0],
                        pageParam[1] > 1 ? pageParam[1] : '',
                        true
                    );
                    break;
                }
            }

            //seo filter
            if (updatedUrl === null) {
                updatedUrl = url.prepareUrl(urlParams);
            }

            link = url.getLink(updatedUrl);

            applyFilter(link, document, updatedUrl);

            e.stopPropagation();
            e.preventDefault();
        });
    };
});
