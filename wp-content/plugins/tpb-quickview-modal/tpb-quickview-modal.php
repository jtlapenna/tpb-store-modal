<?php
/**
 * Plugin Name: TPB QuickView Modal
 * Description: Clean iframe modal for TPB product configuration
 * Version: 1.0.0
 * Author: TPB Team
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('TPB_QV_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TPB_QV_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Enqueue modal assets
 */
function tpb_qv_enqueue_assets() {
    if (is_admin()) return;
    
    // Enqueue modal CSS
    wp_enqueue_style(
        'tpb-qv-modal-css',
        TPB_QV_PLUGIN_URL . 'assets/css/modal.css',
        [],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/css/modal.css')
    );
    
    // Enqueue modal JS
    wp_enqueue_script(
        'tpb-qv-modal-js',
        TPB_QV_PLUGIN_URL . 'assets/js/modal.js',
        ['jquery'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/js/modal.js'),
        true
    );
    
    // Localize script
    wp_localize_script('tpb-qv-modal-js', 'TPB_QV_CONFIG', [
        'home_url' => home_url('/'),
        'qv_param' => 'tpb_qv_iframe'
    ]);
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_assets');

/**
 * Add modal HTML to footer
 */
function tpb_qv_add_modal_html() {
    if (is_admin()) return;
    ?>
    <div id="tpb-qv-overlay" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <iframe id="tpb-qv-iframe" class="tpb-qv-iframe" src="about:blank"></iframe>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'tpb_qv_add_modal_html');
