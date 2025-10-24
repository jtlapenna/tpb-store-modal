<?php
/**
 * Plugin Name: TPB QuickView Modal
 * Description: Individual modal architecture for TPB product configuration
 * Version: 2.0.0
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

    // Enqueue modal template CSS (base styling for all modals)
    wp_enqueue_style(
        'tpb-qv-modal-template-css',
        TPB_QV_PLUGIN_URL . 'assets/css/modal-template.css',
        [],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/css/modal-template.css')
    );

    // Enqueue modal CSS with cache busting (specific overrides)
    wp_enqueue_style(
        'tpb-qv-modal-css',
        TPB_QV_PLUGIN_URL . 'assets/css/modal.css',
        ['tpb-qv-modal-template-css'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/css/modal.css')
    );

    // Enqueue iframe content CSS
    wp_enqueue_style(
        'tpb-qv-iframe-content-css',
        TPB_QV_PLUGIN_URL . 'assets/css/iframe-content.css',
        [],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/css/iframe-content.css')
    );

    // Enqueue all individual handlers (simplified - load all on all pages)
    wp_enqueue_script(
        'tpb-qv-flower-handler',
        TPB_QV_PLUGIN_URL . 'assets/js/modal-flower-handler.js',
        ['jquery'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/js/modal-flower-handler.js'),
        true
    );
    
    wp_enqueue_script(
        'tpb-qv-category-handler',
        TPB_QV_PLUGIN_URL . 'assets/js/modal-category-handler.js',
        ['jquery'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/js/modal-category-handler.js'),
        true
    );
    
    wp_enqueue_script(
        'tpb-qv-quickcheckout-handler',
        TPB_QV_PLUGIN_URL . 'assets/js/modal-quickcheckout-handler.js',
        ['jquery'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/js/modal-quickcheckout-handler.js'),
        true
    );
    
    wp_enqueue_script(
        'tpb-qv-menu-boards-handler',
        TPB_QV_PLUGIN_URL . 'assets/js/modal-menu-boards-handler.js',
        ['jquery'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/js/modal-menu-boards-handler.js'),
        true
    );
    
    wp_enqueue_script(
        'tpb-qv-branded-handler',
        TPB_QV_PLUGIN_URL . 'assets/js/modal-branded-handler.js',
        ['jquery'],
        filemtime(TPB_QV_PLUGIN_PATH . 'assets/js/modal-branded-handler.js'),
        true
    );

    // Localize script with configuration
    wp_localize_script('tpb-qv-flower-handler', 'TPB_QV_CONFIG', [
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
 * Add individual modal HTML to footer
 */
function tpb_qv_add_modal_html() {
    if (is_admin()) return;
    ?>
    <!-- TPB Individual Modals v2.0 -->
    
    <!-- Flower Station Modal -->
    <div id="tpb-qv-flower-modal" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <?php include plugin_dir_path(__FILE__) . 'templates/modal-flower-stations.php'; ?>
        </div>
    </div>
    
    <!-- Category Station Modal -->
    <div id="tpb-qv-category-modal" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <?php include plugin_dir_path(__FILE__) . 'templates/modal-category-stations.php'; ?>
        </div>
    </div>
    
    <!-- Quick Checkout Modal -->
    <div id="tpb-qv-quickcheckout-modal" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <?php include plugin_dir_path(__FILE__) . 'templates/modal-quickcheckout-stations.php'; ?>
        </div>
    </div>
    
    <!-- Menu Boards Modal -->
    <div id="tpb-qv-menu-boards-modal" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <?php include plugin_dir_path(__FILE__) . 'templates/modal-menu-boards.php'; ?>
        </div>
    </div>
    
    <!-- Branded Stations Modal -->
    <div id="tpb-qv-branded-modal" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <?php include plugin_dir_path(__FILE__) . 'templates/modal-branded-stations.php'; ?>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'tpb_qv_add_modal_html');

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
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_iframe_assets', 5);
add_action('wp_head', 'tpb_qv_enqueue_iframe_assets', 1);
add_action('wp_footer', 'tpb_qv_enqueue_iframe_assets', 1);

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

/**
 * AJAX endpoint to get product image
 */
add_action('wp_ajax_tpb_qv_get_product_image', 'tpb_qv_get_product_image');
add_action('wp_ajax_nopriv_tpb_qv_get_product_image', 'tpb_qv_get_product_image');

function tpb_qv_get_product_image() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'tpb_qv_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    $product_id = intval($_POST['product_id']);
    
    if (!$product_id) {
        wp_send_json_error('Invalid product ID');
        return;
    }
    
    // Get product
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error('Product not found');
        return;
    }
    
    // Get featured image
    $image_id = $product->get_image_id();
    
    if ($image_id) {
        $image_url = wp_get_attachment_image_url($image_id, 'full');
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        wp_send_json_success([
            'image_url' => $image_url,
            'image_alt' => $image_alt
        ]);
    } else {
        wp_send_json_error('No featured image found');
    }
}

/**
 * AJAX endpoint to get products for stepper
 */
add_action('wp_ajax_tpb_qv_products', 'tpb_qv_get_products');
add_action('wp_ajax_nopriv_tpb_qv_products', 'tpb_qv_get_products');

function tpb_qv_get_products() {
    $tags = isset($_GET['tags']) ? explode(',', sanitize_text_field($_GET['tags'])) : [];
    $categories = isset($_GET['categories']) ? explode(',', sanitize_text_field($_GET['categories'])) : [];
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 24;
    
    $args = [
        'limit' => $limit,
        'status' => 'publish'
    ];
    
    if (!empty($tags)) {
        $args['tag'] = $tags;
    }
    
    if (!empty($categories)) {
        $args['category'] = $categories;
    }
    
    $products = wc_get_products($args);
    
    $results = array_map(function($product) {
        return [
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'slug' => $product->get_slug(),
            'price_html' => $product->get_price_html(),
            'image_url' => wp_get_attachment_image_url($product->get_image_id(), 'full'),
            'categories' => array_map(function($cat) {
                return ['slug' => $cat->slug, 'name' => $cat->name];
            }, get_the_terms($product->get_id(), 'product_cat') ?: []),
            'tags' => array_map(function($tag) {
                return ['slug' => $tag->slug, 'name' => $tag->name];
            }, get_the_terms($product->get_id(), 'product_tag') ?: [])
        ];
    }, $products);
    
    wp_send_json(['results' => $results]);
}
