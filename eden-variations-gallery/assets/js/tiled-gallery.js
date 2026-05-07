(function () {
    if (!window.jQuery) return;
    var $ = window.jQuery;

    $(function () {
        var $gallery = $('.eden-tiled-gallery').first();
        if (!$gallery.length) return;

        var restBase = $gallery.attr('data-rest-base') || '';
        if (!restBase) return;

        var $form = $('form.variations_form').first();
        if (!$form.length) return;

        var cache = {};
        var inflight = {};

        function render(imgs) {
            if (!imgs || !imgs.length) return;
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
        }

        function fetchVariation(variationId) {
            if (cache[variationId]) {
                render(cache[variationId]);
                return;
            }
            if (inflight[variationId]) return;
            inflight[variationId] = true;

            fetch(restBase + variationId, { credentials: 'same-origin' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    delete inflight[variationId];
                    if (!data || !data.images) return;
                    cache[variationId] = data.images;
                    render(data.images);
                })
                .catch(function () {
                    delete inflight[variationId];
                });
        }

        $form.on('found_variation', function (event, variation) {
            if (!variation || !variation.variation_id) return;
            fetchVariation(variation.variation_id);
        });
    });
})();
