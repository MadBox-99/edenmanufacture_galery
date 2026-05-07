<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Swatches {

    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'inline_dynamic_css'], 20);
    }

    /**
     * Per-color background-image rules — must stay dynamic (vary per product).
     * Attached to the registered swatches.css handle as inline CSS.
     */
    public static function inline_dynamic_css() {
        if (!function_exists('is_product') || !is_product() || !is_single()) return;
        global $product;
        if (!$product || !is_a($product, 'WC_Product') || !$product->is_type('variable')) return;

        $data = Eden_VG_Data::get($product->get_id());
        if (empty($data['colors_seen'])) return;

        $rules = [];
        foreach ($data['colors_seen'] as $color => $info) {
            if (empty($info['medium'])) continue;
            // Strip any character that could break out of the <style> tag or the attribute selector.
            $safe_color = preg_replace('/[<>"\\\\]/', '', wp_strip_all_tags((string) $color));
            if ($safe_color === '') continue;
            $rules[] = sprintf(
                '.variable-items-wrapper[data-attribute_name="attribute_szin"] .variable-item[data-value="%s"]{background-image:url(%s);}',
                $safe_color,
                esc_url_raw($info['medium'])
            );
        }
        if (empty($rules)) return;

        wp_add_inline_style('eden-vg-swatches', implode("\n", $rules));
    }
}
