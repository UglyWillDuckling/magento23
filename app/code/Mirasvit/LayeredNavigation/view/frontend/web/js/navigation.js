define([
    'jquery',
    'Mirasvit_LayeredNavigation/js/action/update-content',
    'Mirasvit_LayeredNavigation/js/navigation/paging',
    'Mirasvit_LayeredNavigation/js/helper/overlay',
    'Mirasvit_LayeredNavigation/js/constants'
], function($, updateContent, initPaging, overlay) {
    /**
     * Widget responsible for initializing AJAX layered navigation, toolbar and paging.
     */
    $.widget('mst.layeredNavigation', {
        cache:   [],

        _create: function () {
            window.mNavigationConfigData = this.options;

            this._bind();
            initPaging();
        },

        _bind: function () {
            $(document).on(window.mNavigationConstants.AJAX_CALL, function (event, ajaxData) {
                var cleanUrl = ajaxData.cleanUrl || window.mNavigationConfigData.cleanUrl;
                var data = ajaxData.data;
                var cachedData = this.getCacheData(data, cleanUrl);

                if (cachedData) {
                    this.updatePage(cachedData);
                } else {
                    this.requestUpdate(cleanUrl, data, this.getCacheId(data, cleanUrl));
                }
            }.bind(this));
        },

        updatePage: function (result) {
            if (window.mNavigationConfigData !== undefined
                && (window.mNavigationConfigData.isSeoFilterEnabled == 1 //seo filter
                    // Representation of attributes in filter clear block
                || window.mNavigationConfigData.isFilterClearBlockInOneRow == 1)) {
                result['url'] = this.getPreparedUrl(window.mNavigationFriendlySeoUrl);
            }

            updateContent(result, window.mNavigationConfigData.isHorizontalByDefault);

            this.addBrowserHistory(result.url);

            initPaging();
        },

        getCacheId: function (data, cleanUrl) {
            var preparedData = data.slice(0);

            preparedData.push(cleanUrl);

            return JSON.stringify(preparedData);
        },

        getCacheData: function (data, cleanUrl) {
            var cacheId = this.getCacheId(data, cleanUrl);

            return this.cache[cacheId];
        },

        createCache: function (cacheId, result) {
            if (window.mNavigationConfigData.isSeoFilterEnabled == 1 //seo filter
                    // Representation of attributes in filter clear block
                || window.mNavigationConfigData.isFilterClearBlockInOneRow == 1) {
                return false;
            }

            this.cache[cacheId] = result;
        },

        addBrowserHistory: function (url) {
            window.history.pushState({url: url}, '', url);

            return true;
        },

        getAjaxPreparedUrl: function () {
            var url = window.mNavigationFriendlySeoUrl;

            if (url.indexOf('?') == -1) {
                url += '?' + window.mNavigationConstants.AJAX_SUFFIX + '=1';
            } else {
                url += '&' + window.mNavigationConstants.AJAX_SUFFIX + '=1';
            }

            url = this.getPreparedUrl(url);

            return url;
        },

        getPreparedUrl: function (url) {
            url = url.replace('%252C', ",");
            return url.replace(/%2C/g, ",");
        },

        requestUpdate: function (cleanUrl, data, cacheId) {
            var self = this;

            overlay.show();

            data.push({name: window.mNavigationConstants.AJAX_SUFFIX, value: 1});

            if (window.mNavigationConfigData !== undefined
                && (window.mNavigationConfigData.isSeoFilterEnabled == 1 //seo filter
                    || window.mNavigationConfigData.isFilterClearBlockInOneRow == 1)
            ) { //Representation of attributes in filter clear block
                cleanUrl = this.getAjaxPreparedUrl();
                data = false;
            }

            $.ajax({
                url:     cleanUrl,
                data:    data,
                cache:   true,
                success: function (result) {
                    var urlWithoutAjax;

                    try {
                        result = $.parseJSON(result);
                        self.createCache(cacheId, result);
                        self.updatePage(result);
                    } catch (e) {
                        if (window.mNavigationAjaxscrollCompatibility !== 'true') {
                            console.log(e);

                            urlWithoutAjax = this.url.replace('?' + window.mNavigationConstants.AJAX_SUFFIX + '=1', '')
                                .replace('&' + window.mNavigationConstants.AJAX_SUFFIX + '=1', '')
                                .replace('&amp;', '&')
                                .replace('%2C', ',');

                            window.location = urlWithoutAjax;
                        }
                    }
                },
                error:   function () {
                    var urlWithoutAjax = this.url.replace('?' + window.mNavigationConstants.AJAX_SUFFIX + '=1', '');

                    urlWithoutAjax = this.url.replace('&' + window.mNavigationConstants.AJAX_SUFFIX + '=1', '');
                    urlWithoutAjax.replace('&amp;', '&');
                    urlWithoutAjax.replace('%2C', ',');
                    window.location = urlWithoutAjax;
                },
                complete: function() {
                    overlay.hide();
                }
            });
        }
    });

    return $.mst.layeredNavigation;
});