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
 * Enqueue modal state management script
 * Must be loaded first to manage z-index for cart elements
 */
function tpb_qv_enqueue_modal_state() {
    if (is_admin()) return;
    
    // Use plugins_url for proper path resolution
    $plugin_dir = plugin_dir_url(__FILE__) . '../plugins/tpb-quickview-modal/assets/js/modal-state.js';
    $plugin_path = plugin_dir_path(__FILE__) . '../plugins/tpb-quickview-modal/assets/js/modal-state.js';

    wp_enqueue_script(
        'tpb-modal-state',
        $plugin_dir,
        [],
        file_exists($plugin_path) ? filemtime($plugin_path) : time(),
        false // Load in header, before modals
    );
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_modal_state', 5); // Early priority

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
