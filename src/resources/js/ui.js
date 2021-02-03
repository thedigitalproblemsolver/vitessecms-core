var ui = {
    activateMenuPanel: function () {
        $('#menu-trigger').on('click', function (e) {
            e.preventDefault();
            $('body').toggleClass('menu-open');
        });
    },
    scrollTo: function (target, top) {
        var position = $(target).offset().top;
        if (typeof top !== 'undefined') {
            position += top;
        }
        $('html,body').animate({
            scrollTop: position
        }, 500);
    },
    addDeleteConfirm: function () {
        $('a.fa-trash').on('click', function (element) {
            element.preventDefault();
            if (confirm("Delete this item?")) {
                ajax.successFunction = "ui.remove('" + this.id.replace('delete_', '') + "')";
                ajax._(this.id);
            }
        })
    },
    addModal: function () {
        $('a.openmodal').on('click', function (element) {
            element.preventDefault();
            ui.modal(this);
        });
    },
    modal: function (element) {
        var url = $(element).attr('href').split('#');

        var check = url[0].split('?');
        var combineCharacter = '?';
        if (check.length === 2) {
            combineCharacter = '&';
        }
        url += combineCharacter + 'embedded=1';

        if (inIframe()) {
            window.location = url;
        } else {
            $.Mustache.load(sys.baseUri+'/mustache/getTemplate?f=partials/modal').done(function () {
                shop.closeMediumCart();
                var modalBox = $('#modal');
                if(modalBox.length === 0 ) {
                    $('body').mustache('partials_modal');
                    modalBox = $('#modal');
                }
                $('#modalIframe').attr('src', 'about:blank').attr('src', url);
                modalBox.modal('show');
            });
        }
    },
    alert: function (msg, type) {
        if (typeof type === "undefined") {
            type = "success";
        }
        $.Mustache.load(sys.baseUri+'/mustache/getTemplate?f=partials/alert').done(function () {
            if ($('.alert-' + type).length === 0) {
                var input = {type: type};
                $('#container-flash').mustache('partials_alert',input);
            }
            ui.fill('.alert-' + type, msg);
        });
    },
    fill: function (selector, filling) {
        if($(selector).length === 0 && inIframe()) {
            window.top.ui.fill(selector, filling);
        } else {
            $(selector).fadeOut(250, function () {
                $(selector).html(filling).fadeIn(250);
            });
        }
    },
    remove: function (Id) {
        $('#' + Id).slideUp(450, function () {
            $('#' + this.id).remove();
        })
    },
    addScrollspy: function() {
        if($('#scrollspy-nav').length > 0 ) {
            ui.scrollSpyOffset = $('.container-topbar').offset().top;
            $('body').addClass('scrollspy');
            $('#scrollspy-nav li:first-child a').addClass('active');
            $('#scrollspy-nav a').on('click', function (e) {
                e.preventDefault();
                var newHeight = ui.scrollSpyOffset;
                $('#scrollspy-nav a.active').removeClass('active');
                $(this).addClass('active');
                ui.scrollTo($(this).attr('href'), ui.scrollSpyOffset * -1);
            });
        }
    },
    addSortable: function () {
        if (typeof $.fn.sortable !== 'undefined') {
            // Sortable rows
            $('.table-sortable').sortable({
                containerSelector: 'table',
                itemPath: '> tbody',
                itemSelector: 'tr',
                placeholder: '<tr class="placeholder"/>',
                handle: '.fa-sort',
                onDrop: function ($item, container, _super, event) {
                    $item.removeClass(container.group.options.draggedClass).removeAttr("style");
                    $("body").removeClass(container.group.options.bodyClass);
                    var dropFunction = $item.parents('.table-sortable').data('sortabledrop');
                    if (dropFunction !== "") {
                        eval(dropFunction);
                    }
                }
            });

            $('.list-group.sortable').sortable({
                delay: 500,
                onDrop: function ($item, container, _super) {
                    var parent = $($item).parents('ol.list-group.sortable')[0];
                    var ordering = $('#' + parent.id).sortable("serialize").get();
                    ordering = JSON.stringify(ordering);
                    var data = {ordering: ordering};
                    ajax._(parent.id, data);
                    _super($item, container);
                }
            });
        }
    },
    addSticky: function () {
        var stickyToggle = function (sticky, stickyWrapper, scrollElement) {
            var stickyHeight = sticky.outerHeight();
            var stickyTop = stickyWrapper.offset().top;
            if (scrollElement.scrollTop() >= stickyTop) {
                stickyWrapper.height(stickyHeight);
                sticky.addClass("is-sticky");
                $('.container-scrollspy-elements, .container-topbar').addClass("is-sticky");
                sticky.css('top', scrollElement.scrollTop() - stickyTop);
            } else {
                $('.container-scrollspy-elements, .container-topbar').removeClass("is-sticky");
                sticky.removeClass("is-sticky");
                stickyWrapper.height('auto');
                sticky.css('top', 0);
            }
        };

        $('[data-toggle="sticky-onscroll"]').each(function () {
            var sticky = $(this);
            var stickyWrapper = $('<div class="sticky-wrapper" ></div>'); // insert hidden element to maintain actual top offset on page
            sticky.before(stickyWrapper);
            sticky.addClass('sticky');

            $(window).on('scroll.sticky-onscroll resize.sticky-onscroll', function () {
                stickyToggle(sticky, stickyWrapper, $(this));
            });

            stickyToggle(sticky, stickyWrapper, $(window));
        });
    },
    addEditor: function () {
        if (typeof $.fn.summernote !== 'undefined') {
            $('textarea.editor').summernote({
                    height: 250,
                    width: '100%',
                    onCreateLink: function (url) {
                        return url;
                    }
                }
            );
        }
    },
    fadeIn: function (sTarget) {
        $(sTarget).addClass('fade-animation').addClass('fade-in').removeClass('fade-out');
    },
    fadeOut: function (sTarget) {
        $(sTarget).addClass('fade-animation').addClass('fade-out').removeClass('fade-in');
    },
    addZoom: function (target) {
        if ($(document).width() > 768) {
            if ($(target).length > 0) {
                var oObject = {image: $(target).attr('src')};
                $.Mustache.load(sys.baseUri+'/mustache/getTemplate?f=partials/image_zoom').done(function () {
                    $(target).parent().mustache('partials_image_zoom', oObject, { method: 'html' });
                    var zoomContainer = $('.container-zoom-image');
                    var zoomImage = $('.container-zoom-image .zoom-image');
                    var zoomImageImg = $('.container-zoom-image .zoom-image img');
                    zoomContainer.on('mouseover', function (e) {
                        var imageSrc = zoomImage.css('background-image').split('?');
                        if(imageSrc.length > 1) {
                            zoomImage.css('background-image', imageSrc[0]);
                        }
                        ui.fadeOut('.zoom-button');
                    });
                    zoomContainer.on('mousemove', function (e) {
                        var offset = zoomContainer.offset();
                        var wScale = parseInt(zoomImageImg.css('content').replace('"', '')) / 2;
                        var hScale = parseInt(zoomImageImg.css('content').replace('"', '')) - 1;

                        var pointerY = e.pageY - offset.top;
                        var newvalueY = ( pointerY - zoomContainer.height() / 2 ) * hScale;
                        zoomImage.css('margin-top', newvalueY * -1);

                        /*var pointerX = e.pageX - offset.top;
                        var newvalueX = pointerX - zoomContainer.width() / wScale;
                        zoomImage.css('margin-left', newvalueX * -1);*/

                    });
                    zoomContainer.on('mouseout', function () {
                        zoomImage.css('margin', 0);
                        ui.fadeIn('.zoom-button');
                    });
                    if (typeof shop !== 'undefined' && $('.container-product-colors').length > 0 ) {
                        shop.parseColorsClick($('.container-product-colors button:first-child'));
                    }
                });
            }
        }
    },
    fillZoomSlider: function(images, imageBase){
        var imageSlider = $('.container-zoom-image-slider');
        imageSlider.fadeOut(200, function () {
            if (images.length > 1) {
                var SliderHtml = '';
                for (i = 0; i < images.length; i++) {
                    SliderHtml += '<a href="#" onclick="return(false);"><img src="' + imageBase + images[i] + '?h=75" /></a>';
                }
                imageSlider.html(SliderHtml).fadeIn();
                $('.container-zoom-image-slider img').on('click', function () {
                    image = $(this).attr('src').split('?');
                    ui.switchZoomImage(image[0]);
                })
            }
        });
    },
    switchZoomImage: function(image) {
        var zoomImage = $('.zoom-image');
        var imageSrc = zoomImage.css('background-image').split('?');
        if(imageSrc !== image) {
            zoomImage.fadeOut(200, function () {
                zoomImage.css('background-image', 'url("' + image + '")').fadeIn(200);
            });
        }
    },
    togglePublishState: function(Id) {
        var el = $('#'+Id.replace('publish_', ''));
        if(el.hasClass('list-group-item-success')) {
            el.addClass('list-group-item-danger');
            el.removeClass('list-group-item-success');
            $('#'+Id).addClass('red');
        } else {
            el.addClass('list-group-item-success');
            el.removeClass('list-group-item-danger');
            $('#'+Id).removeClass('red');
        }
    },
    addLazyLoad: function() {
        if (typeof $.fn.lazyload !== 'undefined') {
            $("img.lazy").lazyload({effect : "fadeIn"});
        }
    },
    addTooltip: function() {
        if (typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    }
};
