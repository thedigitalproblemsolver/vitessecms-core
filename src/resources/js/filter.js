var filter = {
    form: null,
    container: null,
    call:null,
    init: function () {
        filter.form = $('form[name=filter]');
        filter.container = $('.container-filterresult');

        filter.form.find('input[type=text], .select2').on('click', function () {
            filter.openFilter();
        });
        filter.form.on('submit', function () {
            if (filter.call !== null) {
                filter.call.abort();
            }
            ajax.delay = 350;
            filter.call = ajax._(this.id, null, 'block/index/render/');

            return false
        });

        filter.form.find('.form-control, .btn-toggle input').on('keyup change', function () {
            filter.form.trigger('submit');
        });

        filter.form.find('.slider').slider();

        $('#searchTerm').on('focus', function () {
            filter.openFilter();
        });

        ajax._(filter.form.attr('id'), null, 'block/index/render/');
    },
    openFilter: function () {
        $('body').addClass('filter-open').removeClass('filter-closed');
        $('.container-filterresult').css('height', '100%');
        $('#modal').modal('hide');
        shop.closeMediumCart();
        var container = $('.container-advanced-filter');
        if (container.length > 0) {
            var button = $('.container-filter .input-group .fa-tasks');
            button.addClass('fa-upload');
            button.removeClass('fa-tasks');
            newHeight = $(window).height() - container.offset().top;
            container.css('height', newHeight);
        }
    },
    closeFilter: function () {
        $('body').removeClass('filter-open').addClass('filter-closed');
        setTimeout("$('.container-filterresult').css('height','0')", 500);
        var container = $('.container-advanced-filter');
        if (container.length > 0) {
            container.css('height', 0);
            var button = $('.container-filter .input-group .fa-upload');
            button.addClass('fa-tasks');
            button.removeClass('fa-upload');
        }
    },
    toggleFilter: function () {
        if ($('.container-filterresult').height() === 0) {
            filter.openFilter();
        } else {
            filter.closeFilter();
        }
    },
    fillTarget: function (response) {
        if (filter.form.find('#firstRun').val() === '1') {
            if ($('#search-target').length === 0) {
                $.Mustache.load(sys.baseUri+'/mustache/getTemplate?f=blocks/FilterResult/shop_clothing').done(function () {
                    $('body').mustache('blocks_filterresult_shop_clothing', response);
                    $('.toggle-filter').on('click', function (e) {
                        e.preventDefault();
                        filter.toggleFilter();
                    });
                });
            }
            filter.form.find('#firstRun').val(false);
        } else {
            ui.scrollTo('body', $('.container-topbar').height()*-1);
            $('#search-intro').fadeOut().slideUp();
            ui.fadeOut('#search-target');
            if (response.block.results.length > 0) {
                $.Mustache.load(sys.baseUri+'/mustache/getTemplate?f=partials/shop/clothing_small').done(function () {
                    var htmlResult = '';
                    for(i = 0; i < response.block.results.length; i++) {
                        htmlResult += $.Mustache.render('partials_shop_clothing_small',response.block.results[i]);
                    }
                    ui.fill('#search-target', htmlResult);
                    setTimeout('shop.addSizeAndColorOverview()', 500);
                });
            } else {
                ui.fill('#search-target', '<div class="col-12 noresult">' + response.block.noresultText + '</div>');
            }
            ui.fadeIn('#search-target');
            var searchTerm = $('#searchTerm');
            if(searchTerm.val() !== '' && searchTerm.val() !== 'undefined') {
                theGoogle.sendGaPageview(sys.baseUri+'/?search='+searchTerm.val());
            }
        }
    }
};
