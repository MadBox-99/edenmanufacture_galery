<?php
/**
 * Plugin Name: Eden Variations Gallery
 * Description: Color variation grid, tiled gallery, and image swatches for EdenManufacture. Single cached source of variation data.
 * Version:     1.0.0
 * Author:      EdenManufacture
 */

if (!defined('ABSPATH')) exit;

define('EDEN_VG_VERSION', '1.0.0');
define('EDEN_VG_DIR', plugin_dir_path(__FILE__));
define('EDEN_VG_URL', plugin_dir_url(__FILE__));

require_once EDEN_VG_DIR . 'includes/class-cache.php';
require_once EDEN_VG_DIR . 'includes/class-data.php';
require_once EDEN_VG_DIR . 'includes/class-rest.php';
require_once EDEN_VG_DIR . 'includes/class-assets.php';
require_once EDEN_VG_DIR . 'includes/shortcode-color-grid.php';
require_once EDEN_VG_DIR . 'includes/shortcode-tiled-gallery.php';
require_once EDEN_VG_DIR . 'includes/frontend-swatches.php';
require_once EDEN_VG_DIR . 'includes/frontend-misc.php';

Eden_VG_Cache::init();
Eden_VG_Rest::init();
Eden_VG_Assets::init();
Eden_VG_Color_Grid::init();
Eden_VG_Tiled_Gallery::init();
Eden_VG_Swatches::init();
Eden_VG_Misc::init();
