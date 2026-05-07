<?php
if (!defined('ABSPATH')) exit;

class Eden_VG_Tiled_Gallery {

    public static function init() {
        add_shortcode('eden_tiled_gallery', [__CLASS__, 'render']);
    }

    public static function render($atts) {
        if (!function_exists('wc_get_product')) return '';
        global $product;
        if (!$product || !is_a($product, 'WC_Product')) return '';

        $product_id = $product->get_id();
        $data       = Eden_VG_Data::get($product_id);

        $variations_data = [];
        foreach ($data['variations'] as $var_id => $vinfo) {
            if (!empty($vinfo['images'])) $variations_data[$var_id] = $vinfo['images'];
        }

        // Initial images: try preselected variation, else first variation, else product gallery
        $initial = [];
        if ($product->is_type('variable')) {
            $attrs = [];
            foreach ($product->get_variation_attributes() as $attr_name => $options) {
                $key = 'attribute_' . sanitize_title($attr_name);
                if (isset($_GET[$key])) $attrs[$key] = wc_clean(wp_unslash($_GET[$key]));
            }
            if (!empty($attrs) && count($attrs) === count($product->get_variation_attributes())) {
                $data_store = WC_Data_Store::load('product');
                $var_id     = $data_store->find_matching_product_variation($product, $attrs);
                if ($var_id && isset($variations_data[$var_id])) {
                    $initial = $variations_data[$var_id];
                }
            }
            if (empty($initial) && !empty($variations_data)) {
                $initial = reset($variations_data);
            }
        }
        if (empty($initial)) {
            $main_id     = $product->get_image_id();
            $gallery_ids = $product->get_gallery_image_ids();
            $img_ids     = array_filter(array_merge([$main_id], $gallery_ids));
            foreach ($img_ids as $iid) {
                $url = wp_get_attachment_image_url($iid, 'large');
                if (!$url) continue;
                $initial[] = [
                    'id'     => $iid,
                    'url'    => $url,
                    'srcset' => wp_get_attachment_image_srcset($iid, 'large') ?: '',
                ];
            }
        }

        ob_start();
        ?>
        <div class="eden-tiled-gallery"
             data-product-id="<?php echo esc_attr($product_id); ?>"
             data-variations='<?php echo esc_attr(wp_json_encode($variations_data)); ?>'>
          <?php foreach ($initial as $img): ?>
            <div class="eden-tile">
              <img src="<?php echo esc_url($img['url']); ?>"
                   srcset="<?php echo esc_attr($img['srcset']); ?>"
                   sizes="(max-width: 849px) 100vw, 50vw"
                   alt="" loading="lazy">
            </div>
          <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
