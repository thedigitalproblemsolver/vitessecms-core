var ajax = {
    call: null,
    delay:0,
    successFunction: '',
    _: function (Id, data, targetUrl, targetId) {
        var url = null;
        if (typeof data === 'undefined') {
            data = {};
        }

        if (typeof targetId === 'undefined') {
            targetId = '#ajax-target';
        }

        if (Id !== null) {
            var element = $('#' + Id);
            if (typeof element.data('ajaxurl') !== "undefined") {
                url = element.data('ajaxurl');
            } else {
                var HtmlTag = element.prop("tagName").toLowerCase();
                if (HtmlTag === 'form') {
                    data = element.serialize();
                    url = element.attr('action');
                }

                if (HtmlTag === 'a') {
                    url = element.attr('href');
                }
            }
        }

        if (typeof data.url === 'string') {
            url = data.url;
        }

        if (typeof data.successFunction === 'string') {
            ajax.successFunction = data.successFunction;
        }

        if (typeof targetUrl === 'string') {
            url = targetUrl;
        }

        var successFunction = ajax.successFunction;
        ajax.successFunction = '';

        var delay = ajax.delay;
        ajax.delay = 0;

        return $.ajax({
            type: 'POST',
            url: url,
            data: data,
            delay: delay,
            successFunction:successFunction,
            targetId:targetId,
            success: function (response) {
                if (typeof response === 'string' && $(targetId).length > 0) {
                    ui.fill(targetId, response);
                    if (successFunction !== '') {
                        eval(successFunction);
                    }
                }

                if (typeof response === 'object') {
                    if (typeof response.alert === 'string') {
                        ui.fill('#container-flash', response.alert);
                        setTimeout("$('#container-flash').fadeOut(250)", 2000);
                    }
                    if (typeof response.successFunction === 'string' && successFunction === '') {
                        successFunction = response.successFunction;
                    }
                    if (typeof response.result === 'boolean' && response.result === true && successFunction !== ''
                    ) {
                        eval(successFunction);
                    }
                }
            }
        });
    }
};
