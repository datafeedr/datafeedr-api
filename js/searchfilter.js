(function ($) {

    var timer = 0;

    var sel = function (ptr, context) {
        if ($.isFunction(ptr))
            return ptr.apply(context);
        return typeof ptr == "string" ? context.find(ptr) : ptr;
    };

    // Writes text into box, wrapping the first match of re in the highlight element.
    // Text is only ever inserted as text nodes so names are never parsed as HTML.
    var highlight = function (box, text, re, opts) {
        var m = re ? text.match(re) : null;
        if (!m || !m[0].length) {
            box.text(text);
            return;
        }
        box.empty().append(
            document.createTextNode(text.slice(0, m.index)),
            $(opts.highlight).text(m[0]),
            document.createTextNode(text.slice(m.index + m[0].length))
        );
    };

    var filter = function (obj, search, opts) {
        var re = new RegExp(search.replace(/[.*+?^${}()|[\]\\-]/g, "\\$&"),
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
                            highlight(box, val, null, opts);
                        }
                    } else if (val.match(re)) {
                        e.show();
                        if (opts.highlight) {
                            highlight(box, val, re, opts);
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
        $(this).on("keyup", function () {
            filter(this, this.value, opts)
        });
        $(this).on("change", function () {
            filter(this, this.value, opts)
        });
    }


})(window.jQuery);