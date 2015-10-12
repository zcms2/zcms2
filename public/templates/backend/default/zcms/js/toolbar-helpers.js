
jQuery(function () {
    //Check & UnCheck all checkbox
    jQuery("#adminForm .table th .iCheck-helper").click(function () {
        var check_all = $(".check_element");
        if (jQuery(".check_all").is(":checked")) {
            check_all.prop("checked", true);
            check_all.each(function () {
                jQuery(this).parent().addClass("checked").attr("aria-checked", "true");
            });
        } else {
            check_all.prop("checked", false);
            check_all.each(function () {
                jQuery(this).parent().removeClass("checked").attr("aria-checked", "false");
            });
        }
    });

    // Click sorting link when cell clicked
    jQuery("th.sorting").click(function () {
        if ($(this).children("a").length > 0) {
            $(this).children("a")[0].click();
        }
    });

});