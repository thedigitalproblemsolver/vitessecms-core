(function ($) {
    $.fn.extend({
        select2Sortable: function () {
            var select = $(this);
            var select2Sortable = $('#' + $(this).attr('id') + '_select2Sortable');
            if (select2Sortable.length > 0) {
                var Ids = select2Sortable.val().split(',').reverse();
                $(Ids).each(function () {
                    var option = select.find('option[value="' + this + '"]')[0];
                    $(option).prop('selected',true);
                    select.prepend(option);
                });
            }

            select.select2({width: '100%'});

            var ul = select.next('.select2-container').find('.select2-selection__rendered');
            ul.sortable({
                onDrop: function () {
                    $($(ul).find('.select2-selection__choice').get().reverse()).each(function () {
                        var title = $(this).attr('title');
                        var option = select.find('option:contains(\''+title+'\')');
                        select.prepend(option);
                    });

                    $('body').removeClass('dragging');
                }
            });
        }
    });
}(jQuery));
