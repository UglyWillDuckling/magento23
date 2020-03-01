define([
    'jquery',
    'underscore',
    'Mirasvit_LayeredNavigation/js/constants'
], function($, _) {
    "use strict";

    function prepareData(data, filterData) { //multiselect
        window.mPreparedData = [];
        window.isMPreparedDataAdded = [];
        window.mNewData = [];

        //prepare name, because of radio
        $.each(data, function (key) {
            var filterValue, filterName;

            if (filterData !== undefined) {
                filterValue = filterData["filterValue"];
                filterName = filterData["filterName"];
            }
            //uncheck link
            if (filterValue && filterValue !== undefined && filterValue == data[key]["value"]
                && filterName !== undefined && filterName == data[key]["name"]) {
                return true;
            }
            if (window.mPreparedData[data[key]["name"]] === undefined) {
                window.mPreparedData[data[key]["name"]] = data[key]["value"];
            } else {
                window.mPreparedData[data[key]["name"]] += window.mNavigationConstants.MULTISELECT_SEPARATOR
                    + data[key]["value"];

                window.mPreparedData[data[key]["name"]] = _.uniq(window.mPreparedData[data[key]["name"]]
                    .split(window.mNavigationConstants.MULTISELECT_SEPARATOR)).join();
            }
        });

        $.each(data, function (key) {
            if (window.mPreparedData[data[key]["name"]] !== undefined) {
                data[key]["value"] = window.mPreparedData[data[key]["name"]];
                if (window.isMPreparedDataAdded[data[key]["name"] + data[key]["value"]] === undefined) {
                    window.mNewData.push(data[key]);
                    window.isMPreparedDataAdded[data[key]["name"] + data[key]["value"]] = true;
                }
            }
        });

        return window.mNewData;
    }

    /**
     * Method triggers the event listen by navigation which requests
     * new data (based on applied filters) and reloads a page content.
     */
    return function (link, el, cleanUrl, filterData, priceValue, isClearAll) {
        var form = $('form[m-navigation-filter]'),
            data = {},
            state = null,
            stateData = {},
            ajaxData = {};

        try {
            if (typeof link === 'string' || link instanceof String) {
                link = link.replace('&amp;', '&');
                link = link.replace('%2C', ',');
            }

            //seo filter and Representation of attributes in filter clear block
            window.mNavigationFriendlySeoUrl = link;

            if (!el) {
                el = document;
            }

            if (!isClearAll) {
                data = form.serializeArray();
                state = $(window.mNavigationConstants.AJAX_STATE_WRAPPER_INPUT_CLASS);
                stateData = state.serializeArray();

                $.merge(data, stateData);

                if (priceValue) { // use in price slider
                    data = data.filter(function (el) {
                        return el.name !== "price";
                    });
                    data.push({name: 'price', value: priceValue});
                }
            }

            data = prepareData(data, filterData);

            ajaxData = {data: data, cleanUrl: cleanUrl};

            $(el).trigger(window.mNavigationConstants.AJAX_CALL, ajaxData);

            return ajaxData;
        } catch (e) {
            if (link) {
                console.log(e);
                window.location = link;
            }
        }
    };
});