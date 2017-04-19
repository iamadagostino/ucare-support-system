(function ($) {

    $.fn.confirm = function (options, callback) {

        var _target = $(this);

        var _defaults = {
            title: "Are you sure?",
            content: "Are you sure?",
            okay_text: "Yes",
            cancel_text: "No"
        };

        var _settings = $.extend(_defaults, options);

        var _modal = _.template($("script.confirm-modal").html());

        var modal = $(_modal({
            id: _settings.id,
            title: _settings.title,
            content: _settings.content,
            okay_text: _settings.okay_text,
            cancel_text: _settings.cancel_text
        }));

        _target.append(modal);

        modal.modal('show');

        var ignore = false;

        modal.find('button.confirm').on('click', function () {
            callback(true);
            ignore = true;
            modal.modal('hide')
        })

        modal.on('hidden.bs.modal', function () {
            if(!ignore) {
                callback(false);
            }

            modal.remove()
        })

    };

    $.fn.submit = function (options) {

        var _form = $(this);

        var _defaults = {
            method: "post",
            success: function (response) {},
            error: function (xhr, status, error) {},
            complete: function (xhr, status) {},
            extras: {}
        };

        var _settings = $.extend(_defaults, options);

        var _show_errors = function (errors) {
            _form.find(".form-control").each(function(index, element) {
                var field = $(element);
                var container = field.parent();

                if (errors[ field.attr("name") ] !== undefined) {
                    container.append( "<span class=\"help-block\">" + errors[ field.attr("name") ] + "</span>" );
                    container.addClass("has-error");
                }
            });
        };

        var _clear_errors = function () {
            _form.find(".form-control").each(function(index, element) {
                var field = $(element);
                var container = field.parents();

                container.find(".help-block").remove();
                container.removeClass("has-error");
            });
        };

        _clear_errors();

        return $.ajax({
            url: _settings.url + "?action=" + _settings.action + "&" + _form.serialize(),
            method: _settings.method,
            data: _settings.extras,
            success: _settings.success,
            complete: _settings.complete,
            error: function (xhr, status, error) {
                _show_errors(xhr.responseJSON.data);
                _settings.error(xhr, status, error);
            }
        });
    };

})(jQuery);
