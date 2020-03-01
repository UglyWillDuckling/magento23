define([
    'jquery',
    'Mirasvit_LayeredNavigation/js/action/apply-filter',
    'jquery/ui',
    'domReady!'
], function ($, applyFilter) {
    'use strict';
    
    $.widget('mst.mNavigationSlider', {
        _create: function () {
            var from = this.options.from || this.options.min,
                to = this.options.to || this.options.max;

            this.value = this.element.find("." + this.options.id + "-range");
            this.slider = this.element.find("." + this.options.id);

            this.slider.slider({
                range:  true,
                min:    this.options.min,
                max:    this.options.max,
                values: [
                    from,
                    to
                ],
                slide:  this.onSlide.bind(this),
                change: this.onChange.bind(this),
                step:   1
            });

            if (from || to) {
                this.addRangeText(from, to);
                this.setRange(this.options.from, this.options.to, false);
            }
        },
        
        onSlide: function (event, ui) {
            this.addRangeText(ui.values[0], ui.values[1]);
            this.setRange(ui.values[0], ui.values[1], false);
        },
        
        onChange: function (event, ui) {
            this.setRange(ui.values[0], ui.values[1], true);
        },
        
        setRange: function (from, to, apply) {
            var priceValue = parseFloat(from).toFixed(0) + "-" + parseFloat(to).toFixed(0),
                link = this.options.url.replace(this.options.slider_param_template, priceValue);
            
            if (apply !== false) {
                if (this.options.isAjax) {
                    applyFilter(link, false, false, false, priceValue);
                } else {
                    link = link.replace('&amp;', '&');
                    link = link.replace('%2C', ',');
                    window.location.href = link;
                }
            }
        },
        
        addRangeText: function (from, to) {
            from = this.prepareSymbolsAfterComma(from);
            to = this.prepareSymbolsAfterComma(to);
            this.element.find("." + "amount-" + this.options.id).val(this.options.currencySymbol + from
                + " - " + this.options.currencySymbol + to);
        },
        
        prepareSymbolsAfterComma: function (value) {
            return parseFloat(value).toFixed(this.options.numberSymbolsAfterComma);
        }
    });

    return $.mst.mNavigationSlider;
});
