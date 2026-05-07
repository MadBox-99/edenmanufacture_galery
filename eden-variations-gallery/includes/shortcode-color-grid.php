<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Color_Grid {

    public static function init() {
        add_shortcode('color_variations_grid', [__CLASS__, 'render']);
    }

    public static function render($atts) {
        $atts = shortcode_atts([
            'product_ids' => '3197,3308,3925,4366',
            'columns'     => '4',
            'category'    => '',
            'products'    => '',
            'numbers'     => '',
            'group'       => 'false',
        ], $atts);

        $category_labels = [
            'MC'  => 'Monokróm',
            'BC'  => 'Alapszínek',
            'CL'  => 'Színes',
            'LT'  => 'Limitált',
            'CF'  => 'Kávé',
            'GER' => 'Limitált',
        ];
        $category_order = ['MC', 'BC', 'CL', 'LT', 'CF', 'GER'];

        $parse_color = function ($color_name) {
            if (preg_match('/^\s*(\d+)\s*-?\s*([A-Z]+)\s*$/', $color_name, $m)) {
                return ['number' => (int) $m[1], 'code' => strtoupper($m[2])];
            }
            return ['number' => 0, 'code' => ''];
        };

        $parse_numbers = function ($input) {
            $allowed = [];
            foreach (array_filter(array_map('trim', explode(',', $input))) as $part) {
                if (strpos($part, '-') !== false) {
                    [$from, $to] = array_map('intval', explode('-', $part, 2));
                    if ($from > $to) [$from, $to] = [$to, $from];
                    for ($i = $from; $i <= $to; $i++) $allowed[$i] = true;
                } else {
                    $allowed[(int) $part] = true;
                }
            }
            return $allowed;
        };

        $filter_cats     = array_filter(array_map(function ($c) { return strtoupper(trim($c)); }, explode(',', $atts['category'])));
        $filter_products = array_filter(array_map(function ($p) { return strtolower(trim($p)); }, explode(',', $atts['products'])));
        $filter_numbers  = !empty($atts['numbers']) ? $parse_numbers($atts['numbers']) : null;

        $cards_by_cat = [];
        $product_ids  = array_map('intval', array_filter(explode(',', $atts['product_ids'])));

        foreach ($product_ids as $pid) {
            $data = Eden_VG_Data::get($pid);
            if (empty($data['variations'])) continue;

            $pname = strtolower($data['product_name']);
            if (!empty($filter_products) && !in_array($pname, $filter_products, true)) continue;

            foreach ($data['colors_seen'] as $color => $color_data) {
                $parsed = $parse_color($color);
                $cat    = $parsed['code'];
                $num    = $parsed['number'];

                if (!empty($filter_cats) && !in_array($cat, $filter_cats, true)) continue;
                if ($filter_numbers !== null && !isset($filter_numbers[$num])) continue;

                $url = add_query_arg(
                    ['attribute_szin' => rawurlencode($color), 'attribute_meret' => '37'],
                    $data['permalink']
                );

                $cards_by_cat[$cat][] = [
                    'name'   => $data['product_name'],
                    'color'  => $color,
                    'number' => $num,
                    'img'    => $color_data['thumb'],
                    'url'    => $url,
                ];
            }
        }

        if (empty($cards_by_cat)) return '';

        wp_enqueue_style('eden-vg-color-grid');

        $cols      = max(1, intval($atts['columns']));
        $col_class = intval(12 / $cols);
        $group     = filter_var($atts['group'], FILTER_VALIDATE_BOOLEAN);

        $render_cards = function ($cards) use ($col_class) {
            echo '<div class="row row-small">';
            foreach ($cards as $c) {
                $alt = esc_attr($c['name'] . ' - ' . $c['color']);
                printf(
                    '<div class="col medium-%1$d small-6 large-%1$d"><div class="col-inner"><a href="%2$s" class="cv-card">'
                    . '<div class="cv-image"><img src="%3$s" alt="%4$s" loading="lazy" /></div>'
                    . '<div class="cv-info"><p class="cv-product">%5$s</p><p class="cv-color">%6$s</p></div>'
                    . '</a></div></div>',
                    $col_class,
                    esc_url($c['url']),
                    esc_url($c['img']),
                    $alt,
                    esc_html($c['name']),
                    esc_html($c['color'])
                );
            }
            echo '</div>';
        };

        ob_start();
        echo '<div class="color-variations-grid">';
        if ($group) {
            $rendered_labels = [];
            foreach ($category_order as $cat) {
                if (empty($cards_by_cat[$cat])) continue;
                if ($cat === 'GER' && isset($rendered_labels[$category_labels['LT']])) continue;
                $label = $category_labels[$cat] ?? $cat;
                $cards = $cards_by_cat[$cat];
                if ($cat === 'LT' && !empty($cards_by_cat['GER'])) {
                    $cards = array_merge($cards, $cards_by_cat['GER']);
                }
                echo '<h3 class="cv-category-heading">' . esc_html($label) . '</h3>';
                $render_cards($cards);
                $rendered_labels[$label] = true;
            }
        } else {
            $all = [];
            foreach ($category_order as $cat) {
                if (!empty($cards_by_cat[$cat])) {
                    $all = array_merge($all, $cards_by_cat[$cat]);
                }
            }
            $render_cards($all);
        }
        echo '</div>';
        return ob_get_clean();
    }
}
