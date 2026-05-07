(function ($) {
    if (!window.jQuery) return;
    $(function () {
        function autoPickSize() {
            var $form = $('.variations_form');
            if (!$form.length) return;
            var $colorSel = $form.find('select[name="attribute_szin"]');
            var $sizeSel = $form.find('select[name="attribute_meret"]');
            if (!$colorSel.length || !$sizeSel.length) return;
            if (!$colorSel.val() || $sizeSel.val()) return;
            var firstSize = $sizeSel.find('option[value!=""]').filter(function () {
                return $(this).val() !== '';
            }).first().val();
            if (!firstSize) return;
            $sizeSel.val(firstSize).trigger('change');
        }

        setTimeout(autoPickSize, 400);

        $(document).on('change', '.variations_form select[name="attribute_szin"]', function () {
            setTimeout(autoPickSize, 100);
        });
    });
})(jQuery);
