<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Data {

    public static function get($product_id) {
        $product_id = (int) $product_id;
        if (!$product_id) return self::empty_data();

        $cached = Eden_VG_Cache::get($product_id);
        if ($cached !== null) return $cached;

        $product = wc_get_product($product_id);
        if (!$product || !$product->is_type('variable')) {
            $empty = self::empty_data();
            Eden_VG_Cache::set($product_id, $empty);
            return $empty;
        }

        $variations   = [];
        $colors_seen  = [];
        $product_name = $product->get_name();
        $permalink    = $product->get_permalink();

        foreach ($product->get_children() as $var_id) {
            $variation = wc_get_product($var_id);
            if (!$variation) continue;

            $color = $variation->get_attribute('szin');

            $rtwpvg = get_post_meta($var_id, 'rtwpvg_images', true);
            if (is_string($rtwpvg) && trim($rtwpvg) !== '') {
                $img_ids = array_filter(array_map('intval', explode(',', $rtwpvg)));
            } elseif (is_array($rtwpvg)) {
                $img_ids = array_filter(array_map('intval', $rtwpvg));
            } else {
                $img_ids = [];
            }
            if (empty($img_ids)) {
                $img_ids = array_filter([$variation->get_image_id()]);
            }

            $large_images = [];
            foreach ($img_ids as $iid) {
                $url = wp_get_attachment_image_url($iid, 'large');
                if (!$url) continue;
                $large_images[] = [
                    'id'     => $iid,
                    'url'    => $url,
                    'srcset' => wp_get_attachment_image_srcset($iid, 'large') ?: '',
                ];
            }

            $variations[$var_id] = [
                'color'  => $color,
                'images' => $large_images,
            ];

            if ($color && !isset($colors_seen[$color])) {
                $main_img_id = $variation->get_image_id();
                if (!$main_img_id && !empty($img_ids)) $main_img_id = (int) $img_ids[0];

                $thumb_url = $main_img_id ? wp_get_attachment_image_url($main_img_id, 'woocommerce_thumbnail') : '';
                if (!$thumb_url) $thumb_url = wc_placeholder_img_src('woocommerce_thumbnail');

                $colors_seen[$color] = [
                    'var_id' => (int) $var_id,
                    'thumb'  => $thumb_url,
                    'medium' => $main_img_id ? wp_get_attachment_image_url($main_img_id, 'medium') : '',
                ];
            }
        }

        $data = [
            'product_name' => $product_name,
            'permalink'    => $permalink,
            'variations'   => $variations,
            'colors_seen'  => $colors_seen,
        ];
        Eden_VG_Cache::set($product_id, $data);
        return $data;
    }

    private static function empty_data() {
        return [
            'product_name' => '',
            'permalink'    => '',
            'variations'   => [],
            'colors_seen'  => [],
        ];
    }
}
