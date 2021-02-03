var form = {
    recaptchaToken:null,
    init: function () {
        var forms = $('form');
        forms.attr('novalidate', 'novalidate');

        var formIndex = 0;
        forms.each(function() {
            if ($(this).attr('id') === '' || typeof $(this).attr('id') === 'undefined') {
                $(this).attr('id','form_' + formIndex);
                formIndex++;
            }

            if ($('#' + $(this).attr('id') + ' .form-control:required').length > 0) {
                $('#' + $(this).attr('id') + ' .btn-success').prop('disabled', true);
            }
        });

        forms.on('submit', function () {
            if($('#'+this.id+' .g-recaptcha').length === 1) {
                return (form.recaptchaToken === $('#'+this.id+' .recaptcha-token').val());
            }

            return form.submitForm(this);
        });

        $('.form-control').on('change blur focus keyup', function () {
            var formElement = $(this).closest('form');
            if( formElement.attr('name') !== 'adminFilter' && formElement.attr('name') !== 'filter' ) {
                $('#' + formElement.attr('id') + ' .form-control:required').each(function () {
                    form.validateField(this.id, formElement.attr('id'));
                });
                $('#' + formElement.attr('id') + ' .btn-success').prop('disabled', true);
                if ($('#' + formElement.attr('id')).find('.is-invalid').length === 0) {
                    $('#' + formElement.attr('id') + ' .btn-success').prop('disabled', false);
                }
            }
        });

        $('input[type=checkbox][readonly]').on('click',function(e){
            e.preventDefault();
        });
        if ( $.isFunction($.fn.select2) ) {
            $('.select2').each(function(){
                var placeholder = '';
                if( $(this).attr('placeholder') !== 'undefined' ) {
                    placeholder = $(this).attr('placeholder');
                }
                $(this).select2({
                    placeholder: placeholder
                });
            });

            if (typeof $.fn.sortable !== 'undefined') {
                $('.select2-sortable').each(function() {
                    $(this).select2Sortable();
                });
            }

            $('.select2-ajax').select2({
                ajax: {
                    url:function() {
                        return $(this).data('url');
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; },
                minimumInputLength: 2,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });

            $('.select2-selection__rendered').each(function(){
                $(this).html($(this).attr('title'));
            });

            function formatRepoSelection (repo) {
                return repo.name;
            }

            function formatRepo (repo) {
                return repo.name;
            }
        }
        form.addEmptyForm();
    },
    validateField: function (elId, formId) {
        var valid = false;
        var targetId = '#'+formId+' #' + elId;
        var el = $(targetId);
        if (typeof el.val() === 'string' && el.val().trim() !== "" && el.val().length > 0 ) {
            switch (el.attr('type')) {
                case 'email':
                    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                    if (re.test(el.val())) {
                        valid = true;
                    }
                    break;
                case 'checkbox':
                    if (el.prop('checked')) {
                        valid = true;
                    }
                    break;
                case 'password':
                    if (el.val().length > 7) {
                        valid = true;
                    }
                    var password2Element = $('#'+formId+' #password2');
                    if( password2Element.length > 0 ) {
                        valid = false;
                        var passwordElement = $('#'+formId+' #password');
                        if(passwordElement.val().length > 7 && password2Element.val().length > 7 && password2Element.val() === passwordElement.val() ) {
                            valid = true;
                        }
                    }
                    break;
                default:
                    valid = true;
                    break;
            }
        }

        if (typeof el.val() === 'object' && el.val().length > 0 ) {
            valid = true;
        }

        if (valid) {
            el.addClass('is-valid').removeClass('is-invalid');
        } else {
            el.addClass('is-invalid').removeClass('is-valid');
        }
    },
    addEmptyForm: function () {
        $('.btn-form-emtpy').on('click', function (el) {
            el.preventDefault();

            var formToReset = $($(this).closest('form'));
            formToReset.find('.form-control').val('');
            if(formToReset.data('ajaxfunction') !== '' ) {
                formToReset.trigger('submit');
            }
        });
    },
    submitForm: function(formElement) {
        if($(formElement).data('ajax') === false) {
            form.recaptchaToken = null;
            return true;
        }

        var formId = $(formElement).attr('id');
        var form = $('#' + formId);
        if( form.attr('name') !== 'filter' && form.attr('target') !== '_blank' ) {
            var useAjax = true;
            $('#' + formId + ' .form-control').each(function () {
                if($(this).prop('required') === true ) {
                    form.validateField($(this).attr('id'), formId);
                }
                if( $(this).attr('type') === 'file' && $(this).hasClass('file-check') && $(this).val() !== '') {
                    useAjax = false;
                }
            });

            if(!useAjax) {
                form.recaptchaToken = null;
                return true;
            }

            if (form.children('.is-invalid').length === 0) {
                ajax._(formId);
                return false;
            }
            ui.scrollTo('#' + formId+' #' + form.children('.is-invalid')[0].id, -120);
            return false;
        }
        form.recaptchaToken = null;

        return true;
    }
};

function parseRecaptcha (token) {
    form.recaptchaToken = token;
    $('.g-recaptcha').closest('form').find('.recaptcha-token').val(token);
}
