<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Cache {

    const VERSION = 'v2';
    const TTL     = DAY_IN_SECONDS;

    public static function init() {
        add_action('save_post_product',                    [__CLASS__, 'invalidate']);
        add_action('woocommerce_save_product_variation',   [__CLASS__, 'invalidate_variation']);
        add_action('woocommerce_delete_product_variation', [__CLASS__, 'invalidate_variation']);
        add_action('woocommerce_trash_product_variation',  [__CLASS__, 'invalidate_variation']);
        add_action('updated_post_meta',                    [__CLASS__, 'maybe_invalidate_on_meta'], 10, 4);
        add_action('added_post_meta',                      [__CLASS__, 'maybe_invalidate_on_meta'], 10, 4);
    }

    public static function key($product_id) {
        return 'eden_var_' . self::VERSION . '_' . (int) $product_id;
    }

    public static function get($product_id) {
        $cached = get_transient(self::key($product_id));
        return is_array($cached) ? $cached : null;
    }

    public static function set($product_id, $data) {
        set_transient(self::key($product_id), $data, self::TTL);
    }

    public static function invalidate($product_id) {
        delete_transient(self::key($product_id));
    }

    public static function invalidate_variation($variation_id) {
        $parent = wp_get_post_parent_id($variation_id);
        if ($parent) self::invalidate($parent);
    }

    public static function maybe_invalidate_on_meta($meta_id, $object_id, $meta_key, $_meta_value) {
        if ($meta_key !== 'rtwpvg_images' && $meta_key !== '_thumbnail_id') return;
        $type = get_post_type($object_id);
        if ($type === 'product') {
            self::invalidate($object_id);
        } elseif ($type === 'product_variation') {
            $parent = wp_get_post_parent_id($object_id);
            if ($parent) self::invalidate($parent);
        }
    }
}
