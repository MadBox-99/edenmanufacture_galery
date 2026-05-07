(function ($) {
    if (!window.jQuery) return;

    var CATEGORY_LABELS = window.EdenVGSwatchLabels || {};
    var CATEGORY_ORDER = ['MC', 'BC', 'CL', 'LT', 'CF'];

    function extractCategory(text) {
        if (!text) return '';
        var m = /([A-Z]+)\s*$/.exec(text.trim());
        var cat = m ? m[1] : '';
        return cat === 'GER' ? 'LT' : cat;
    }

    function groupSwatches() {
        var $wrappers = $('.variable-items-wrapper[data-attribute_name="attribute_szin"]');
        if (!$wrappers.length) return;
        $wrappers.each(function () {
            var $wrap = $(this);
            if ($wrap.data('cv-grouped')) return;
            var $items = $wrap.children('.variable-item');
            if (!$items.length) return;

            var groups = {};
            $items.each(function () {
                var val = $(this).attr('data-value') || $(this).attr('data-title') || $(this).text();
                var cat = extractCategory(val);
                if (!cat) cat = 'OTHER';
                (groups[cat] = groups[cat] || []).push(this);
            });

            $items.detach();
            CATEGORY_ORDER.forEach(function (cat) {
                if (!groups[cat] || !groups[cat].length) return;
                $wrap.append('<h4 class="cv-swatch-group-heading">' + (CATEGORY_LABELS[cat] || cat) + '</h4>');
                groups[cat].forEach(function (el) { $wrap.append(el); });
            });
            if (groups.OTHER && groups.OTHER.length) {
                groups.OTHER.forEach(function (el) { $wrap.append(el); });
            }

            $wrap.data('cv-grouped', true);
        });
    }

    $(function () {
        setTimeout(groupSwatches, 250);
        $(document).on('woo_variation_swatch_before_reload_variation', groupSwatches);
    });
})(jQuery);
