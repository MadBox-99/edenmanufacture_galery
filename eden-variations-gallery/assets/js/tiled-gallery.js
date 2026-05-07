(function () {
    if (!window.jQuery) return;
    var $ = window.jQuery;

    $(function () {
        var $gallery = $('.eden-tiled-gallery').first();
        if (!$gallery.length) return;

        var data;
        try { data = JSON.parse($gallery.attr('data-variations') || '{}'); }
        catch (e) { return; }

        var $form = $('form.variations_form').first();
        if (!$form.length) return;

        $form.on('found_variation', function (event, variation) {
            if (!variation || !variation.variation_id) return;
            var imgs = data[variation.variation_id];
            if (!imgs || !imgs.length) return;

            // Build via DOM API to avoid HTML injection from any field.
            var frag = document.createDocumentFragment();
            imgs.forEach(function (img) {
                var tile = document.createElement('div');
                tile.className = 'eden-tile';
                var el = document.createElement('img');
                el.src = String(img.url || '');
                if (img.srcset) el.srcset = String(img.srcset);
                el.sizes = '(max-width: 849px) 100vw, 50vw';
                el.alt = '';
                el.loading = 'lazy';
                tile.appendChild(el);
                frag.appendChild(tile);
            });
            $gallery.empty().append(frag);
        });
    });
})();
