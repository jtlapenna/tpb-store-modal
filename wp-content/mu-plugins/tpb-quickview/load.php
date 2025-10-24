<?php
/**
 * TPB QuickView MU-Plugin Loader
 * Enqueues stepper scripts with cache busting
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('TPB_QV_MU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TPB_QV_MU_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Enqueue cart integration script
 * Universal cart update functionality for all modals
 */
function tpb_qv_enqueue_cart_integration() {
    if (is_admin()) return;

    // Enqueue cart integration script
    wp_enqueue_script(
        'tpb-cart-integration',
        TPB_QV_MU_PLUGIN_URL . '../plugins/tpb-quickview-modal/assets/js/cart-integration.js',
        ['jquery'],
        filemtime(TPB_QV_MU_PLUGIN_PATH . '../plugins/tpb-quickview-modal/assets/js/cart-integration.js'),
        true
    );
    
    // Localize script with WooCommerce cart parameters
    wp_localize_script('tpb-cart-integration', 'tpb_cart_params', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
        'cart_hash' => WC()->cart->get_cart_hash(),
        'fragments_nonce' => wp_create_nonce('wc_fragments'),
        'add_to_cart_nonce' => wp_create_nonce('woocommerce_add_to_cart')
    ]);
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_cart_integration');

// Include API endpoint
require_once TPB_QV_MU_PLUGIN_PATH . 'api-qv.php';
