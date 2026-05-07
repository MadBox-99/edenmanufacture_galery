<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Assets {

    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'register'], 5);
    }

    public static function register() {
        $css = EDEN_VG_URL . 'assets/css/';
        $js  = EDEN_VG_URL . 'assets/js/';
        $v   = EDEN_VG_VERSION;

        // Always registered (not enqueued unless needed)
        wp_register_style('eden-vg-color-grid',      $css . 'color-grid.css',      [], $v);
        wp_register_style('eden-vg-tiled-gallery',   $css . 'tiled-gallery.css',   [], $v);
        wp_register_style('eden-vg-swatches',        $css . 'swatches.css',        [], $v);
        wp_register_style('eden-vg-swatch-grouping', $css . 'swatch-grouping.css', [], $v);

        wp_register_script('eden-vg-auto-pick-size',  $js . 'auto-pick-size.js',  ['jquery'], $v, true);
        wp_register_script('eden-vg-swatch-grouping', $js . 'swatch-grouping.js', ['jquery'], $v, true);
        wp_register_script('eden-vg-sticky-info',     $js . 'sticky-info.js',     [],         $v, true);
        wp_register_script('eden-vg-tiled-gallery',   $js . 'tiled-gallery.js',   ['jquery'], $v, true);

        // Enqueue site-wide CSS for the color grid only when shortcode is used
        // (handled via wp_enqueue_style call in the shortcode itself).

        if (function_exists('is_product') && is_product() && is_single()) {
            wp_enqueue_style('eden-vg-tiled-gallery');
            wp_enqueue_style('eden-vg-swatch-grouping');
            wp_enqueue_style('eden-vg-swatches');

            wp_enqueue_script('eden-vg-auto-pick-size');
            wp_enqueue_script('eden-vg-sticky-info');
            wp_enqueue_script('eden-vg-tiled-gallery');

            // Localized labels for swatch grouping
            $lang = function_exists('pll_current_language') ? pll_current_language() : 'hu';
            $labels = ($lang === 'de') ? [
                'MC'  => 'Monochrom',
                'BC'  => 'Grundfarben',
                'CL'  => 'Bunt',
                'LT'  => 'Limitiert',
                'GER' => 'Limitiert',
                'CF'  => 'Kaffee',
            ] : [
                'MC'  => 'Monokróm',
                'BC'  => 'Alapszínek',
                'CL'  => 'Színes',
                'LT'  => 'Limitált',
                'GER' => 'Limitált',
                'CF'  => 'Kávé',
            ];
            wp_enqueue_script('eden-vg-swatch-grouping');
            wp_localize_script('eden-vg-swatch-grouping', 'EdenVGSwatchLabels', $labels);
        }
    }
}
