<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Misc {

    public static function init() {
        add_filter('woocommerce_attribute_label', [__CLASS__, 'translate_attribute_label_de'], 10, 3);
    }

    public static function translate_attribute_label_de($label, $name = '', $product = null) {
        if (!function_exists('pll_current_language')) return $label;
        if (pll_current_language() !== 'de') return $label;

        $map = [
            'szin'     => 'Farbe',
            'meret'    => 'Größe',
            'pa_szin'  => 'Farbe',
            'pa_meret' => 'Größe',
            'Szín'     => 'Farbe',
            'Méret'    => 'Größe',
        ];
        $key_name = is_string($name) ? sanitize_title($name) : '';
        if (isset($map[$key_name])) return $map[$key_name];
        if (isset($map[$label]))    return $map[$label];
        return $label;
    }
}
