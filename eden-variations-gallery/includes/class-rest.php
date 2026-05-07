<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Rest {

    const NAMESPACE = 'eden-vg/v1';

    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route(self::NAMESPACE, '/variation/(?P<variation_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'get_variation_images'],
            'permission_callback' => '__return_true',
            'args'                => [
                'variation_id' => [
                    'sanitize_callback' => 'absint',
                    'validate_callback' => function ($v) { return is_numeric($v) && (int) $v > 0; },
                ],
            ],
        ]);
    }

    public static function get_variation_images(WP_REST_Request $request) {
        $variation_id = (int) $request['variation_id'];
        $parent_id    = (int) wp_get_post_parent_id($variation_id);
        if (!$parent_id) {
            return new WP_REST_Response(['images' => []], 404);
        }

        $data = Eden_VG_Data::get($parent_id);
        $images = isset($data['variations'][$variation_id]['images'])
            ? $data['variations'][$variation_id]['images']
            : [];

        $response = new WP_REST_Response(['images' => $images], 200);
        // Public 1h cache — invalidate via cache version bump on data change.
        $response->header('Cache-Control', 'public, max-age=3600');
        return $response;
    }

    public static function url_for_product($product_id) {
        return rest_url(self::NAMESPACE . '/variation/');
    }
}
