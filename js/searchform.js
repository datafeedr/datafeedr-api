jQuery(function($) {

    // Search form

    function getPopup() {
        var p = $("#dfrapi_popup");
        if(p.length)
            return p;
        $("body").append($("<div id='dfrapi_popup' class='reveal-modal'><div class='reveal-modal-content'></div><a class='close-reveal-modal'>&#215;</a></div>"));
        return $("#dfrapi_popup");
    }

    function searchFormAjax(command, params, fn) {
        params = $.extend({}, params, {
            action: 'search_form',
            command: command,
            useSelected: $("#dfrapi_useSelected").val()
        });
        $.post(ajaxurl, params, fn);
    }

    function checkSelects(form) {
        form.find(".value2").each(function() {
            if($(this).closest(".filter").find(".operator select").val() == "between")
                $(this).show();
            else
                $(this).hide();
        });
    }

    function updateSearchForm(form) {
        var active = 0;

        form.find(".filter").each(function() {
            var filter = $(this);
            var fieldName = filter.attr("class").match(/filter_(\w+)/)[1];
            filter.find(".field select").val(fieldName);
            if(filter.is(":visible"))
                active++;
        });

        $(".dfrapi_choose_box").each(function() {
            var box = $(this);
            var kind = box.attr("rel");
            var value = box.find("input").val();
            searchFormAjax("names_" + kind, {value: value}, function(s) {
                box.find(".names").html(s);
            });
        });

        checkSelects(form);

        var btns = $("#dfrapi_search_form .filter .minus");
        if(active == 1)
            btns.addClass("disabled");
        else
            btns.removeClass("disabled");
    }

    function ajaxPopup(btn, command) {
        var input = $(btn).closest(".dfrapi_choose_box").find("input");
        var form  = $(btn).closest("form");

        var popup = getPopup();
        popup.find(".reveal-modal-content").html($("#dfprs_loading_content").html());
        popup.reveal({
            animation: 'fade',
            animationspeed: 100
        });
        searchFormAjax(command, {value: input.val()}, function(response) {
            popup.find(".reveal-modal-content").html(response);
            popup.find("a.button.reset_search").on("click", function() {
                $(this).parent().find("input").val("").change();
                return false;
            });
            popup.find("a.button.submit").on("click", function() {
                var value = [];
                popup.find("input:checked").each(function() {
                    value.push($(this).val());
                });
                input.val(value.join(","));
                popup.trigger("reveal:close");
                updateSearchForm(form);
                return false;
            });
            popup.find(".filter_action input").searchFilter({
                element: popup.find(".inline_frame_element"),
                subject: ".element_name",
                highlight: "<span style='background: yellow'>"
            });

        });
    }

    var fcount = 1000;

    function cloneFilter(flt) {
        var f = flt.clone(true);
        fcount++;
        f.find(":input").each(function() {
            this.name = this.name.replace(/\[\d+\]/g, "[" + fcount + "]");
            $(this).val("")
        });
        f.find(".names").html("");
        return f;
    }

    $("#dfrapi_search_form .filter .minus").on("click", function() {
        if($(this).is(".disabled"))
            return false;
        var form = $(this).closest("form");
        if(form.find(".filter:visible").length)
            $(this).closest(".filter").hide();
        updateSearchForm(form);
        return false;
    });

    $("#dfrapi_search_form_filter").on("click", function() {
        var form = $(this).closest("form");
        var hiddenFilters = form.find(".filter:hidden");
        var last = form.find(".filter:last");
        if(hiddenFilters.length) {
            last.after($(hiddenFilters[0]).show());
        } else {
            last.after(cloneFilter(last));
        }
        updateSearchForm(form);
        return false;
    });

    $("#dfrapi_search_form .filter .field select").on("change", function() {
        var form = $(this).closest("form");
        var fieldName = $(this).val();

        var thisFilter = $(this).closest(".filter"), otherFilter;
        var others = $(".filter_" + fieldName + ":hidden");

        if(others.length)
            otherFilter = $(others[0]);
        else {
            others = $(".filter_" + fieldName);
            otherFilter = cloneFilter($(others[0]));
        }

        otherFilter.insertBefore(thisFilter);
        thisFilter.hide();
        otherFilter.show();

        updateSearchForm(form);
    });

    $("#dfrapi_search_form .filter .operator select").on("change", function() {
        checkSelects($(this).closest("form"));
    });

    $("#dfrapi_search_form").closest("form").on("submit", function() {
        $(this).find("#dfrapi_search_form .filter:hidden").remove();
    });

    $("#dfrapi_search_form .choose_network").on("click", function() {
        ajaxPopup(this, "choose_network");
    });

    $("#dfrapi_search_form .choose_merchant").on("click", function() {
        ajaxPopup(this, "choose_merchant");
    });
    
    $(".dfrapi_search_help").on('click',function(e) {
        var classes = $(this).closest('.filter').attr('class').split(' ');
		for(var i=0; i<classes.length; i++) {
			if (classes[i].match("^filter_")) {
		   		var filter = classes[i];
		   		$('.'+filter+' > .help').slideToggle();
		   	}
		}
        e.preventDefault();
    });

    updateSearchForm($("#dfrapi_search_form").closest("form"));
});
