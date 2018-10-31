
    $("div.mod_socialmedia input.form-check-input.custom-control-input").on("change", function(e) {
        e.preventDefault();
        $.ajax({
            type    : 'POST',
            url     : CAT_ADMIN_URL + '/admintools/tool/coreSocialmedia/enable',
            data    : {
                item: $(this).data("item"),
                url: $(this).data("url"),
                enabled: $(this).is(":checked")
            },
            dataType: 'json',
            success : function(data, status) {
                if(data.success) {
                    BCGrowl($.cattranslate(data.message),data.success);
                } else {
                    BCGrowl($.cattranslate(data.message));
                }
            }
        });

    });