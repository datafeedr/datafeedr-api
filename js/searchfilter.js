(function ($) {

    var timer = 0;

    var sel = function (ptr, context) {
        if ($.isFunction(ptr))
            return ptr.apply(context);
        return typeof ptr == "string" ? context.find(ptr) : ptr;
    };

    var filter = function (obj, search, opts) {
        var re = new RegExp(search.replace(/[.\\\\+*?\[\]\{\}\(\)-]/g, "\\$&"),
            opts.caseSensitive ? "" : "i");
        var ctx = sel(opts.element, obj);
        var n = 0;
        var size = opts.stepSize || 100;

        var step = function () {
            for (var i = 0; i < size; i++) {

                if (n >= ctx.length) {
                    if (opts.after)
                        opts.after.apply(ctx);
                    return;
                }

                var e = $(ctx[n++]);

                var box = opts.subject ? sel(opts.subject, e) : this;
                var val = box.text();

                if (!e.hasClass('hidden')) {
                    if (!search.length) {
                        e.show();
                        if (opts.highlight) {
                            box.html(val);
                        }
                    } else if (val.match(re)) {
                        e.show();
                        if (opts.highlight) {
                            box.html(val.replace(re, opts.highlightRe));
                        }
                    } else {
                        e.hide();
                    }
                }
            }
            timer = setTimeout(step, 1);
        };

        clearTimeout(timer);
        step();

    };

    $.fn.searchFilter = function (opts) {
        if (opts.highlight) {
            opts.highlightRe = $("<div>@</div>").wrapInner(opts.highlight).html().replace(/@/g, "$$&");
        }
        $(this).on("keyup", function () {
            filter(this, this.value, opts)
        });
        $(this).on("change", function () {
            filter(this, this.value, opts)
        });
    }


})(window.jQuery);