var admin = {
    formAjaxCall: null,
    init: function () {
        $("form[data-ajaxFunction]").on('submit', function () {
            if (admin.formAjaxCall !== null) {
                admin.formAjaxCall.abort();
            }
            ajax.successFunction = "setTimeout('ui.addModal()',400)";
            admin.formAjaxCall = ajax._(this.id);

            return false;
        });
        $("form[data-ajaxFunction] input").on('keyup', function () {
            $("form[data-ajaxFunction]").submit();
        });
        $("form[data-ajaxFunction] select").on('click', function () {
            $("form[data-ajaxFunction]").submit();
        });
        $('form[name=adminToolbarForm] input').on('change', function () {
            $('form[name=adminToolbarForm]').submit();

        });
        $('form[name=adminToolbarForm]').on('submit', function () {
            ajax._(this.id);
            admin.checkAdminState();
            return false;
        });
        $('.openfilemanager').on('click', function (e) {
            e.preventDefault();
            $('.note-toolbar').hide();
            var url = $(this).attr('href') + '?embedded=1&target=' + $(this).attr('id');
            var filemanager = $('#container-filemanager');
            if (filemanager.length === 0) {
                var html = '<div id="container-filemanager" name="container-filemanager" class="fixed-top fixed-bottom"><iframe src="' + url + '"></iframe></div>';
                $(html).appendTo('body');
                filemanager = $('#container-filemanager');
                /*if(!inIframe()) {
                    filemanager.addClass('fixed-top fixed-bottom');
                }*/
            } else {
                filemanager.find('iframe').attr('src', url);
            }
            filemanager.slideDown();
        });

        $('#table-size-and-color .delete').on('click', function (e) {
            e.preventDefault();
            admin.sizeAndColorDelete($(this).attr('id'))
        });
        $('#size-and-color-add-button').on('click', function (e) {
            e.preventDefault();
            var newRow = $('#size-and-color-baserow').clone();
            var id = 'newRow_' + new Date().getTime();

            newRow.attr('id', id);
            newRow.find('.select2-container').remove();
            newRow.find('.select2-hidden-accessible').removeClass('select2-hidden-accessible').addClass('add-select2');
            newRow.find('.color-picker').addClass('add-colorpicker');
            newRow.find('.delete').attr('id', 'delete_' + id);
            newRow.appendTo('#table-size-and-color');

            var html = $('#' + id).html();
            html = html.replace(/__key__/g, id);
            $('#' + id).html(html);

            $('.add-colorpicker').colorpicker().removeClass('add-colorpicker');
            $('.add-select2').select2().removeClass('add-select2');
            $('#' + id + ' .delete').on('click', function (e) {
                e.preventDefault();
                admin.sizeAndColorDelete($(this).attr('id'))
            });
        });

        if ($.isFunction($.fn.colorpicker)) {
            $('.colorpicker').colorpicker();
        }
        admin.checkAdminState();
        admin.shopPriceCalculator();
        admin.addPublish();
        admin.initNewsletter();
        admin.initGridUi();
        ui.addSortable();
        ui.addEditor();
    },
    initNewsletter: function () {
        $('#sendPreviewEmail').on('click', function (e) {
            e.preventDefault();
            if ($('#previewEmail').val() !== '') {
                data = {previewEmail: $('#previewEmail').val()};
                ajax._('sendPreviewEmail', data, sys.baseUri + '/admin/communication/adminnewsletter/sendPreview/' + $('#newsletterId').html())
            }
        });

        $('#queueNewsletter').on('click', function (e) {
            e.preventDefault();
            ajax._('queueNewsletter', {}, sys.baseUri + '/admin/communication/adminnewsletter/queueNewsletter/' + $('#newsletterId').html())
        });
    },
    initGridUi: function () {
        var layoutEditor = $('#layout_editor');
        if ($.isFunction($.fn.gridEditor) && layoutEditor.length > 0) {
            layoutEditor.gridEditor({
                new_row_layouts: [[12], [6, 6], [9, 3], [3, 9], [3, 3, 3, 3]],
                content_types: ['summernote'],
                summernote: {
                    config: {
                        callbacks: {
                            onInit: function () {
                                var element = this;
                            }
                        }
                    }
                }
            });
            $('#layout_editor_fields a').on('click', function (e) {
                e.preventDefault();
                $('.note-editable').append('[FIELD_' + $(this).attr('id') + ']<br />');
            });
            $('#layout_editor_button_save').on('mouseover', function (e) {
                $('#html').val(layoutEditor.gridEditor('getHtml'));
            });
        }
    },
    checkAdminState: function () {
        if ($('#layoutMode').prop('checked')) {
            $('body.admin').addClass('layout-on');
        } else {
            $('body.admin').removeClass('layout-on');
        }
        if ($('#editorMode').prop('checked')) {
            $('body.admin').addClass('editor-on');
        } else {
            $('body.admin').removeClass('editor-on');
        }
    },
    saveSorting: function (elementId) {
        var data = {};
        data.order = [];
        $('#' + elementId + ' tr').each(function () {
            data.order.push(this.id)
        });
        ajax._(elementId, data);
    },
    sizeAndColorDelete: function (id) {
        if (confirm("Delete this item?")) {
            ui.remove(id.replace('delete_', ''));
        }
    },
    shopPriceCalculator: function () {
        if ($('#taxrate').length > 0) {
            $('#pricesale').on('keyup', function () {
                $('#price').val((parseFloat($('#pricesale').val()) / 100) * (100 - parseInt($("#taxrate option:selected").text())));
            });
        }
    },
    addPublish: function () {
        $('.publish-toggle').on('click', function (element) {
            element.preventDefault();
            ajax.successFunction = "ui.togglePublishState('" + this.id + "')";
            ajax._(this.id);
        })
    }
};
