var theGoogle = {
    init: function () {
        if (typeof ga !== 'undefined') {
            $('.gaEvent').on('click', function () {
                theGoogle.sendGaEvent($(this).data('category'), $(this).data('action'), $(this).data('label'));
            })
        }
        theGoogle.initMaps();
    },
    checkoutStep: function (step) {
        if (typeof ga !== 'undefined') {
            ga('ec:setAction', 'checkout', {'step': step});
            ga('send', 'pageView');
        }
    },
    sendGaEvent: function (category, action, label) {
        if (typeof ga !== 'undefined') {
            var realLabel = '';
            if (
                typeof category !== 'undefined'
                && typeof action !== 'undefined'
            ) {
                if (typeof label !== 'undefined') {
                    realLabel = label
                }
                ga('send', 'event', category, action, realLabel);
            }
        }
    },
    sendGaPageview: function (url) {
        if (typeof ga !== 'undefined' && typeof url !== 'undefined') {
            ga('send', 'pageview', url);
        }
    },
    initMaps: function() {
        if (typeof GMaps !== 'undefined') {
            var zoom = 7;
            var gmaps = $('.container-gmaps');

            if(gmaps.data('zoom') !== undefined) {
                zoom = gmaps.data('zoom');
            }
            var map = new GMaps({
                div: '.container-gmaps',
                lat: 52.092876,
                lng: 5.104480,
                zoom: zoom
            });
            var markerIcon = '';

            if(
                gmaps.data('markericon') !== undefined
                && gmaps.data('markericon') !== ''
            ) {
                markerIcon = gmaps.data('markericon');
            }

            var centerToMarker = false;
            var gmapsAddresses = $('.gmaps-addresses address');
            if(gmapsAddresses.length === 1) {
                centerToMarker = true
            }
            gmapsAddresses.each(function(){
                var container = $(this);
                var name = container.find('b[itemprop="name"]').html();
                var street = container.find('span[itemprop="streetAddress"]').html();
                var postalCode = container.find('span[itemprop="postalCode"]').html();
                var addressLocality = container.find('span[itemprop="addressLocality"]').html();
                var latitude = container.find('meta[itemprop="latitude"]').attr('content');
                var longitude = container.find('meta[itemprop="longitude"]').attr('content');

                if( typeof longitude === 'undefined' || typeof latitude === 'undefined') {
                    GMaps.geocode({
                        address: street + ' ' + postalCode + ' ' + addressLocality,
                        callback: function (results, status) {
                            if (status === 'OK') {
                                var latlng = results[0].geometry.location;
                                itemId = container.attr('id').split('_');
                                ajax._(
                                    null,
                                    {
                                        id: itemId[1],
                                        latitude:latlng.lat(),
                                        longitude:latlng.lng()
                                    },
                                    sys.baseUri+'/content/index/setGeoCoordinates'
                                );

                                map.addMarker({
                                    lat: latlng.lat(),
                                    lng: latlng.lng(),
                                    title: name,
                                    infoWindow: {
                                        content: container.html()
                                    },
                                    icon: markerIcon,
                                    click: function(e) {
                                        theGoogle.sendGaEvent('maps','click',this.title);
                                    }
                                });
                                if (centerToMarker) {
                                    map.setCenter(latlng.lat(), latlng.lng())
                                }
                            }
                        }
                    });
                } else {
                    var marker = map.addMarker({
                        lat: latitude,
                        lng:longitude,
                        title: name,
                        infoWindow: {
                            content: container.html()
                        },
                        icon: markerIcon,
                        click: function(e) {
                            theGoogle.sendGaEvent('maps','click',this.title);
                        }
                    });
                    if (centerToMarker) {
                        map.setCenter(latlng.lat(), latlng.lng())
                    }
                }
            });
        }
    }
};
