(function ($) {
    $.ZFormValidation = function (element, options) {

        var defaults = {
            class_error : "invalid",
            class_valia : "valid"
        };

        var plugin = this;

        plugin.opts = [];

        var $element = $(element);

        plugin.init = function () {
            plugin.opts = $.extend({}, defaults, options);
            var inputFields = [];
            $element.find("input, textarea, select, fieldset, button").each(function () {
                var $el = $(this),
                    id = $el.attr("id"),
                    tagName = $el.prop("tagName").toLowerCase();
                if ($el.hasClass("required")) {
                    $el.attr("aria-required", "true").attr("required", "required")
                }
                if ((tagName === "input" || tagName === "button") && $el.attr("type") === "submit") {
                    if ($el.hasClass("validate")) {
                        $el.on("click", function () {
                            return isValid(form)
                        })
                    }
                } else {
                    if (tagName !== "fieldset") {
                        $el.on("blur", function () {
                            return validate(this)
                        });
                        if ($el.hasClass("validate-email") && inputEmail) {
                            $el.get(0).type = "email"
                        }
                    }
                    $el.data("label", plugin.findLabel(id, form));
                    inputFields.push($el)
                }
            });
            $($element).data("inputfields", inputFields)
        };

        plugin.findLabel = function (id, form) {
            var $label, $form = jQuery(form);
            if (!id) {
                return false
            }
            $label = $form.find("#" + id + "-lbl");
            if ($label.length) {
                return $label
            }
            $label = $form.find('label[for="' + id + '"]');
            if ($label.length) {
                return $label
            }
            return false
        };

        plugin.init();

    };

    $.fn.ZFormValidation = function (options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {
            var plugin = new $.ZFormValidation(this, options);
            $(this).data('ZFormValidation', plugin);
        });
    };
})(jQuery);