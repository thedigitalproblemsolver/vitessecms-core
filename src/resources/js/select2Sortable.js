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

            select.select2({
                width: '100%',
                createTag: function () {
                    return undefined;
                }
            });

            var ul = select.next('.select2-container').find('.select2-selection__rendered');
            ul.sortable({
                onDrop: function () {
                    $($(ul).find('.select2-selection__choice').get().reverse()).each(function () {
                        var id = $(this).data('data').id;
                        var option = select.find('option[value="' + id + '"]')[0];
                        select.prepend(option);
                    });

                    $('body').removeClass('dragging');
                }
            });
        }
    });
}(jQuery));
