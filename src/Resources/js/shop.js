var shop = {
    orderMessageCall: null,
    init: function () {
        shop.addShipto();
        shop.addSizeAndColor();
        shop.addSizeAndColorOverview();
        shop.addCart();
        shop.setCheckout();
        shop.setPacking();
        $('form[name=changeOrderState] select').on('change', function () {
            $(this).closest('form').submit();
        });

        if ($('.shopcart-content').length > 0) {
            ajax.successFunction = "ui.fill('.shopcart-content', response.cartText);";
            ajax._(null, {}, 'shop/cart/getcarttext/');
        }

        $('#orderMessage').on('keyup', function () {
            ajax.delay = 500;
            if (shop.orderMessageCall !== null) {
                shop.orderMessageCall.abort();
            }
            shop.orderMessageCall = ajax._('orderMessageForm');
        });
    },
    addCart: function () {
        var shopcartShowmore = $('.shopcart-showmore');
        shopcartShowmore.on('mouseover', function () {
            shop.addMediumCart();
        });
        shopcartShowmore.on('click', function (e) {
            e.preventDefault();
            shop.addMediumCart();
            if ($('body').hasClass('cart-medium-open')) {
                shop.closeMediumCart();
            } else {
                shop.openMediumCart();
            }
        });
    },
    addMediumCart: function () {
        var cart = $('#container-cart-medium');
        if (cart.length === 0) {
            $('body').append('<div id="container-cart-medium" class="container-cart-medium"><iframe id="iframe-cart-medium" src="about:blank"></iframe></div>');
            var btnCheckout = $('.container-shopcart .btn-checkout').clone().removeClass('btn-checkout').addClass('btn-block');
            cart.append(btnCheckout);
        }
    },
    openMediumCart: function () {
        $('#iframe-cart-medium').attr('src', 'shop/cart/index?embedded=1');
        $('body').addClass('cart-medium-open');
        $('.shopcart-showmore').children('.fa').removeClass('fa-eye').addClass('fa-close');
        $('#modal').modal('hide');
        filter.closeFilter();
    },
    closeMediumCart: function () {
        $('body').removeClass('cart-medium-open');
        $('.shopcart-showmore').children('.fa').removeClass('fa-close').addClass('fa-eye');
        $('#iframe-cart-medium').attr('src', 'about:blank');
    },
    addShipto: function () {
        $('.container-checkout-shipto .btn').on('click', function () {
            $('.container-checkout-shipto blockquote.open').removeClass('open');
            if ($(this).data('id') !== '') {
                $('#shipto-' + $(this).data('id')).addClass('open');
            }
            $('.container-checkout-shipto .btn-success').removeClass('btn-success').addClass('btn-info');
            $(this).addClass('btn-success');

            data = {
                url: 'shop/checkout/setShiptoAddress/',
                id: $(this).data('id')
            };
            ajax._(null, data);
        });

        var shipto = $('.container-checkout-shipto');
        if (shipto.data('selected') !== '') {
            $('.container-checkout-shipto .btn[data-id="' + shipto.data('selected') + '"]').click();
        }
    },
    addSizeAndColor: function () {
        if ($('.container-product-colors').length > 0 && $('.container-product-sizes').length > 0) {
            $('.container-product-colors button').on('click', function (e) {
                e.preventDefault();
                shop.parseSizeAndColor(this, '.container-product-sizes', '.container-product-colors');
                shop.parseColorsClick($(this));
            });

            $('.container-product-sizes button').on('click', function (e) {
                e.preventDefault();
                shop.parseSizeAndColor(this, '.container-product-colors', '.container-product-sizes');
            });

            var colorFirstChild = $('.container-product-colors button:first-child');
            colorFirstChild.addClass('selected');
            shop.parseSizeAndColor(colorFirstChild, '.container-product-sizes', '.container-product-colors');
            $('.container-product-sizes button:first-child:not(.disabled)').click();
        }
    },
    parseColorsClick: function (element) {
        var images = element.data('image').split(',');
        var imageBase = element.data('imagebase');
        var zoomImage = $('.zoom-image');
        var ZoomImageImage = $('.zoom-image img');
        var shouldReplace = true;
        if (ZoomImageImage.length > 0) {
            var imageSrc = ZoomImageImage.attr('src').split('?');

            for (i = 0; i < images.length; i++) {
                if (imageSrc[0] === imageBase + images[i]) {
                    shouldReplace = false;
                }
            }
        }

        if (shouldReplace) {
            var firstImage = imageBase + images[0];
            if (ZoomImageImage.length > 0) {
                if (ZoomImageImage.attr('src') !== firstImage) {
                    zoomImage.fadeOut(200, function () {
                        ZoomImageImage.attr('src', firstImage);
                        zoomImage.css('background-image', 'url("' + firstImage + '")').fadeIn();
                    });
                }
            } else {
                var imageAddZoom = $('.image-add-zoom');
                if (imageAddZoom.attr('src') !== firstImage) {
                    imageAddZoom.attr('src', firstImage);
                }
            }
        }

        ui.fillZoomSlider(images, imageBase);
    },
    addSizeAndColorOverview: function () {
        $('.container-colors div').on('mouseover', function () {
            var image = $(this).parents('.card').find('.card-img-top img');
            var imageParts = $(this).data('image').split('?');
            var images = $(this).data('image').split(',');
            var imageSrc = images[0];
            if (images.length > 1 && imageParts.length > 1) {
                imageSrc += '?' + imageParts[1];
            }
            if (image.attr('src') !== imageSrc) {
                image.fadeOut(300, function () {
                    image.attr('src', imageSrc);
                    image.fadeIn(300);
                });
            }
        });
    },
    parseSizeAndColor: function (element, crossContainer, container) {
        var input = $(element).data('sku');
        $(container + ' button').removeClass('selected');
        $(element).addClass('selected').removeClass('disabled');

        var skus = [];
        if (substr_count(input, ',') === 0) {
            skus = [input];
        } else {
            skus = input.split(',');
        }

        $(crossContainer + ' button').addClass('disabled');
        for (i = 0; i < skus.length; i++) {
            $(crossContainer + " button[data-sku*='" + skus[i] + "']").removeClass('disabled');
        }
        $(crossContainer + ' .disabled').removeClass('selected');

        shop.setVariation();
    },
    setVariation: function () {
        var selectedColor = $('.container-product-colors .selected');
        var selectedSize = $('.container-product-sizes .selected');
        if (selectedColor.length > 0 && selectedSize.length > 0) {
            var colorSkus = csvStringToArray(selectedColor.data('sku'));
            var sizeSkus = csvStringToArray(selectedSize.data('sku'));

            var variationSku = '';
            if (colorSkus.length < sizeSkus.length) {
                variationSku = $.grep(sizeSkus, function (el) {
                    return $.inArray(el, colorSkus) !== -1;
                })
            } else {
                variationSku = $.grep(colorSkus, function (el) {
                    return $.inArray(el, sizeSkus) !== -1;
                })
            }

            $('.container-addtocart .btn-success').prop('disabled', false);
            $('#variation').val(variationSku);
            $('#quantity').attr('max', $('#stock_' + variationSku).val()).val(1);
        } else {
            $('.container-addtocart .btn-success').prop('disabled', true);
            $('#variation').val(null);
        }
    },
    setCheckout: function () {
        $('.checkout-steps a').on('click', function (e) {
            e.preventDefault();
        });

        var activeSteps = $('.checkout-steps .active');
        activeSteps.prevAll('li').each(function () {
            $(this).addClass('active');
            var $link = $(this).find('a');
            $link.attr('href', $link.data('slug')).unbind('click');
        });
        if (typeof theGoogle !== 'undefined') {
            theGoogle.checkoutStep(activeSteps.index() + 1);
        }
    },
    setPacking: function () {
        var inputFields = $('.container-cart-packing input');
        inputFields.on('click', function () {
            $(this).closest('form')[0].submit();
        });
        inputFields.each(function () {
            var el = $(this);
            if (el.val() === el.data('value')) {
                el.prop('checked', true);
            }
        })
    }
};
