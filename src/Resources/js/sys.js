var sys = {
    templates: {},
    baseUri: '',
    init: function () {
        sys.baseUri =  $('base').attr('href').replace(/\/$/, '');
        sys.rewriteLinks();

        ui.activateMenuPanel();
        ui.addDeleteConfirm();
        ui.addModal();
        ui.addZoom('.image-add-zoom');
        ui.addSticky();
        ui.addScrollspy();
        ui.addLazyLoad();
        ui.addTooltip();

        form.init();
        if (typeof admin !== 'undefined') {
            admin.init();
        }
        if (typeof shop !== 'undefined') {
            shop.init();
        }
        if (typeof filter !== 'undefined') {
            filter.init();
        }
        if (typeof site !== 'undefined') {
            site.init();
        }
        if (typeof theGoogle !== 'undefined') {
            theGoogle.init();
        }

        $('.load-block').each(function () {
            if($(this).data('block') !== '') {
                ajax._(
                    null,
                    {'blockId': $(this).data('block')},
                    'block/index/renderHtml/',
                    '#' + $(this).attr('id')
                );
            }
        });

        setTimeout("$('#container-flash').fadeOut(250)", 2000);
    },
    rewriteLinks: function () {
        if (inIframe()) {
            $("a[href!='" + sys.baseUri + "*']").each(function () {
                $(this).attr('href', addParam('embedded','1', $(this).attr('href')));
            });
        }
    },
    redirect: function (url) {

    }
};

$(function () {
    sys.init();
});
