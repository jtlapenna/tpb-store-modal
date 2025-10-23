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

    // Enqueue modal CSS with aggressive cache busting
    wp_enqueue_style(
        'tpb-qv-modal-css',
        TPB_QV_PLUGIN_URL . 'assets/css/modal.css',
        [],
        time() . '.' . rand(1000, 9999)
    );

    // Enqueue iframe content CSS with aggressive cache busting
    wp_enqueue_style(
        'tpb-qv-iframe-content-css',
        TPB_QV_PLUGIN_URL . 'assets/css/iframe-content.css',
        [],
        time() . '.' . rand(1000, 9999)
    );

    // Enqueue clean modal JS with aggressive cache busting
    wp_enqueue_script(
        'tpb-qv-modal-js',
        TPB_QV_PLUGIN_URL . 'assets/js/modal-clean.js',
        ['jquery'],
        time() . '.' . rand(1000, 9999),
        true
    );

    // Localize script with CPB integration
    wp_localize_script('tpb-qv-modal-js', 'TPB_QV_CONFIG', [
        'home_url' => home_url('/'),
        'qv_param' => 'tpb_qv_iframe',
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tpb_qv_nonce'),
        'cpb_enabled' => class_exists('Addify_Composite_Product'),
        'plugin_url' => TPB_QV_PLUGIN_URL,
        'debug' => defined('WP_DEBUG') && WP_DEBUG
    ]);
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_assets');

/**
 * Add modal HTML to footer
 */
function tpb_qv_add_modal_html() {
    if (is_admin()) return;
    ?>
    <!-- TPB Modal v<?php echo time(); ?> - Cache Busted -->
    <div id="tpb-qv-overlay" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <?php
            // Detect modal type from URL
            $current_url = $_SERVER['REQUEST_URI'];
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            
            // Modal type detection
            $is_flower_station = strpos($current_url, 'flower-station-configure-now') !== false || 
                                strpos($referer, 'flower-station') !== false;
            
            $is_category_station = strpos($current_url, 'category-station-configure-now') !== false || 
                                  strpos($current_url, 'category-station') !== false ||
                                  strpos($referer, '#category-stations') !== false ||
                                  strpos($referer, 'category-station') !== false;
            
            $is_quick_checkout = strpos($current_url, 'quick-checkout-station-configure-now') !== false || 
                                strpos($current_url, 'quick-checkout-configure-now') !== false ||
                                strpos($current_url, 'quick-checkout') !== false ||
                                strpos($referer, '#br-qco-mb') !== false ||
                                strpos($referer, 'quick-checkout') !== false;
            
            $is_menu_boards = strpos($current_url, 'menu-boards') !== false || 
                             strpos($referer, 'menu-boards') !== false ||
                             strpos($referer, 'configure-menu-boards') !== false;
            
            $is_branded_displays = strpos($current_url, 'branded') !== false || 
                                  strpos($referer, 'branded') !== false ||
                                  strpos($referer, 'configure-branded') !== false;
            
            // Include appropriate template
            if ($is_quick_checkout) {
                include plugin_dir_path(__FILE__) . 'templates/modal-quickcheckout-stations.php';
            } elseif ($is_menu_boards) {
                include plugin_dir_path(__FILE__) . 'templates/modal-menu-boards.php';
            } elseif ($is_branded_displays) {
                include plugin_dir_path(__FILE__) . 'templates/modal-branded-stations.php';
            } elseif ($is_category_station) {
                include plugin_dir_path(__FILE__) . 'templates/modal-category-stations.php';
            } else {
                // Default to Flower Station
                include plugin_dir_path(__FILE__) . 'templates/modal-flower-stations.php';
            }
            ?>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'tpb_qv_add_modal_html');

/**
 * CPB Integration Hooks
 */
function tpb_qv_cpb_integration() {
    if (!class_exists('Addify_Composite_Product')) return;
    
    // Add modal trigger to CPB buttons
    add_action('woocommerce_single_product_summary', 'tpb_qv_add_cpb_modal_trigger', 25);
    
    // Handle CPB form submissions in iframe
    add_action('wp_ajax_tpb_qv_cpb_add_to_cart', 'tpb_qv_handle_cpb_add_to_cart');
    add_action('wp_ajax_nopriv_tpb_qv_cpb_add_to_cart', 'tpb_qv_handle_cpb_add_to_cart');
}
add_action('init', 'tpb_qv_cpb_integration');

/**
 * Add iframe class to body when in iframe mode
 */
function tpb_qv_add_iframe_class($classes) {
    if (isset($_GET['tpb_qv_iframe']) && $_GET['tpb_qv_iframe'] === '1') {
        $classes[] = 'tpb-qv-iframe';
    }
    return $classes;
}
add_filter('body_class', 'tpb_qv_add_iframe_class');

/**
 * Enqueue iframe-specific assets when in iframe mode
 */
function tpb_qv_enqueue_iframe_assets() {
    if (!isset($_GET['tpb_qv_iframe']) || $_GET['tpb_qv_iframe'] !== '1') {
        return;
    }

    // Enqueue the iframe content CSS
    wp_enqueue_style(
        'tpb-qv-iframe-content',
        TPB_QV_PLUGIN_URL . 'assets/css/iframe-content.css',
        [],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/css/iframe-content.css')
    );
    // Ensure class-based gating is available in iframe regardless of theme
    wp_add_inline_style('tpb-qv-iframe-content', 'body.tpb-qv-iframe .tpb-hidden{display:none !important;} body.tpb-qv-iframe form.cart .tpb-hidden{display:none !important;}');

    // OLD CPB SCRIPT DISABLED - Native stepper now handles all iframe functionality via MU plugin
    // The native stepper (wp-content/mu-plugins/tpb-quickview/assets/js/tpb-qv-native.js) replaces this
    
    /*
    // DISABLED: Old progressive disclosure system
    wp_enqueue_script(
        'tpb-qv-iframe-js',
        TPB_QV_PLUGIN_URL . 'assets/js/iframe-progressive-v2.js',
        ['jquery'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/js/iframe-progressive-v2.js'),
        true
    );

    // DISABLED: Old inline UI cleanup
    $inline = <<<'JS'
(function(){
	function run(){
		try{
			// Remove helper text paragraph
			Array.from(document.querySelectorAll('p,div,h3,h4')).forEach(function(n){ if((n.textContent||'').trim().toLowerCase()==='choose skus, mount, and finish.') n.remove(); });
			// Remove instock and view product text/links
			Array.from(document.querySelectorAll('.af_cp_green, .stock_msg, .view_product_comp_compi.desc, .view_product_comp_compi')).forEach(function(n){ n.remove(); });
			// Move Add to Quote to end of cart form
			var cart = document.querySelector('form.cart');
			var atq = document.querySelector('.yith-ywraq-add-to-quote, [class*="add-to-quote"]');
			if(cart && atq && !cart.contains(atq)){ cart.appendChild(atq); }
			// Move quantity to end of cart form
			if(cart){ var qty = cart.querySelector('.quantity'); if(qty && !qty.nextElementSibling){ cart.appendChild(qty); } }
		}catch(e){}
	}
	if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded', run);} else { run(); }
	// Retry a few times as CPB renders dynamically
	var i=0; var t=setInterval(function(){ i++; run(); if(i>10) clearInterval(t); }, 300);
})();
JS;
    wp_add_inline_script('tpb-qv-iframe-js', $inline, 'after');
    */
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_iframe_assets', 5);
add_action('wp_head', 'tpb_qv_enqueue_iframe_assets', 1);
add_action('wp_footer', 'tpb_qv_enqueue_iframe_assets', 1);

/**
 * Add modal trigger to CPB buttons
 */
function tpb_qv_add_cpb_modal_trigger() {
    global $product;
    
    if (!$product || !$product->is_type('composite')) return;
    
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Find CPB configure buttons and add modal trigger
        $('.single_add_to_cart_button, .afc_add_to_cart_button').each(function() {
            const $btn = $(this);
            const href = $btn.attr('href') || window.location.href;
            
            if (href.includes('/product/')) {
                $btn.attr('data-tpb-modal', 'true');
                $btn.attr('data-product-url', href);
                console.log('✅ CPB button wired for modal:', $btn.text().trim());
            }
        });
    });
    </script>
    <?php
}

/**
 * Handle CPB add to cart from iframe
 */
function tpb_qv_handle_cpb_add_to_cart() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'tpb_qv_nonce')) {
        wp_die('Security check failed');
    }
    
    // Process the CPB add to cart
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
    
    if ($cart_item_key) {
        wp_send_json_success([
            'message' => 'Product added to cart successfully',
            'cart_url' => wc_get_cart_url()
        ]);
    } else {
        wp_send_json_error('Failed to add product to cart');
    }
}