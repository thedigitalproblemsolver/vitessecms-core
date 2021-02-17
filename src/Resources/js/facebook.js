var facebook = {
    addEvent: function (action, input) {
        if (typeof fbq !== 'undefined') {
            if (typeof action !== 'undefined') {
                var params = [];
                if (typeof input === 'object') {
                    for (var key in input) {
                        if (input.hasOwnProperty(key)) {
                            params.push(key + ":" + input[key]);
                        }
                    }
                }
                if (params.length > 0) {
                    fbq('track', action, params);
                } else {
                    fbq('track', action);
                }
            } else {
                $('.fbEvent').each(function () {
                    if ($(this).data('value')) {
                        fbq('track', $(this).data('action'), {value: $(this).data('value'), currency: 'EUR'});
                    } else {
                        fbq('track', $(this).data('action'));
                    }
                    $(this).remove();
                });
            }

        }
    },
    addToCart: function (itemId) {
        var input = {};
        input.content_ids = '['+itemId+']';
        input.content_type = 'product';
        facebook.addEvent('AddToCart', input);
    }
};
