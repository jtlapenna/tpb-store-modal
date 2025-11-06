<?php
/**
 * Plugin Name: TPB QuickView Modal
 * Description: Individual modal architecture for TPB product configuration
 * Version: 2.4.1
 * Author: TPB Team
 */

if (!defined('ABSPATH')) exit;

// -----------------------------------------------------------------------------
// Constants
// -----------------------------------------------------------------------------
define('TPB_QV_PLUGIN_URL',  plugin_dir_url(__FILE__));
define('TPB_QV_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Force-unique version while debugging to beat CDN edge cache.
if (!defined('TPB_QV_DEV_VERSION')) {
    define('TPB_QV_DEV_VERSION', (defined('WP_DEBUG') && WP_DEBUG) ? (string) time() : '');
}

/**
 * Version helper:
 * - In debug: return a unique epoch (kill edge cache)
 * - Else: use filemtime() for reliable cache-busting
 */
function tpb_qv_ver($mtime_or_string) {
    return (TPB_QV_DEV_VERSION !== '') ? TPB_QV_DEV_VERSION : $mtime_or_string;
}

// -----------------------------------------------------------------------------
// Early neutralizer (remove stale assets before we enqueue ours)
// -----------------------------------------------------------------------------
add_action('wp_enqueue_scripts', function () {
    $stale_scripts = [
        'tpb-qv-modal-state',        // legacy handle variants
        'tpb-qv-native',
        'tpb-qv-native-category',
        'tpb-qv-native-quickcheckout',
        'tpb-cart-fix',
        'tpb-qv-fix-cart-z',         // Old script
        'tpb-qv-cart-portal',        // Old script
    ];
    foreach ($stale_scripts as $h) {
        wp_dequeue_script($h);
        wp_deregister_script($h);
    }
}, 1);

// -----------------------------------------------------------------------------
// Enqueue canonical assets
// -----------------------------------------------------------------------------
function tpb_qv_enqueue_assets() {
    if (is_admin()) return;

    $asset_versions = [
        'styles'  => [],
        'scripts' => [],
    ];

    // ---- Styles ----
    // NOTE: iframe-content.css is now ONLY enqueued in iframe mode (see tpb_qv_enqueue_iframe_assets)
    $styles = [
        ['tpb-qv-modal-template-css', 'assets/css/modal-template.css', []],
        ['tpb-qv-modal-css',          'assets/css/modal.css',          ['tpb-qv-modal-template-css']],
    ];
    foreach ($styles as [$handle, $rel, $deps]) {
        $path = TPB_QV_PLUGIN_PATH . $rel;
        if (!file_exists($path)) continue;

        $ver = tpb_qv_ver(filemtime($path));
        wp_enqueue_style($handle, TPB_QV_PLUGIN_URL . $rel, $deps, $ver);
        $asset_versions['styles'][$handle] = $ver;
    }

    // ---- Scripts ----
    // Modal state (canonical) FIRST
    $state_rel  = 'assets/js/modal-state.js';
    $state_path = TPB_QV_PLUGIN_PATH . $state_rel;
    if (file_exists($state_path)) {
        $ver = tpb_qv_ver(filemtime($state_path));
        wp_enqueue_script(
            'tpb-modal-state',
            TPB_QV_PLUGIN_URL . $state_rel,
            ['jquery'],
            $ver,
            true
        );
        $asset_versions['scripts']['tpb-modal-state'] = $ver;

        // Prepare config, allow overrides
        $product_ids = apply_filters('tpb_qv_product_ids', [
            'flower-station'        => 4607, // ✅ update if different
            'category-station'      => 4833, // ✅ update if different
            'quickcheckout-station' => 4849, // ✅ update if different
            'menu-board'            => 0,    // 0/falsey means “no product id”
            'branded-station'       => 0,
        ]);

        $fallback_images = apply_filters('tpb_qv_fallback_images', [
            'flower-station'        => TPB_QV_PLUGIN_URL . 'assets/img/fallback-flower.jpg',
            'category-station'      => TPB_QV_PLUGIN_URL . 'assets/img/category-station-hero.jpg',
            'quickcheckout-station' => TPB_QV_PLUGIN_URL . 'assets/img/quickcheckout-hero.jpg',
            'menu-board'            => TPB_QV_PLUGIN_URL . 'assets/img/menu-boards-hero.jpg',
            'branded-station'       => TPB_QV_PLUGIN_URL . 'assets/img/branded-station-hero.jpg',
        ]);

        wp_localize_script('tpb-modal-state', 'TPB_QV_CONFIG', [
            'home_url'    => home_url('/'),
            'qv_param'    => 'tpb_qv_iframe',
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('tpb_qv_nonce'),
            'cpb_enabled' => class_exists('Addify_Composite_Product'),
            'plugin_url'  => TPB_QV_PLUGIN_URL,
            'debug'       => defined('WP_DEBUG') && WP_DEBUG,

            // Canonical product IDs used by each modal handler (normalized keys)
            'product_ids'     => $product_ids,

            // Image fallbacks (handlers try these if no product image found)
            'fallback_images' => $fallback_images,
        ]);

        // Tiny version banner for field debugging
        wp_add_inline_script(
            'tpb-modal-state',
            'try{console.log("[TPB] assets", ' . wp_json_encode($asset_versions) . ');}catch(e){}',
            'after'
        );
    }

    // Handlers (depend on modal state). Keep idempotent in JS.
    $handlers = [
        'modal-flower-handler.js',
        'modal-category-handler.js',
        'modal-quickcheckout-handler.js',
        'modal-menu-boards-handler.js',
        'modal-branded-handler.js',
    ];
    foreach ($handlers as $file) {
        $rel  = 'assets/js/' . $file;
        $path = TPB_QV_PLUGIN_PATH . $rel;
        if (!file_exists($path)) continue;

        $handle = 'tpb-qv-' . str_replace(['modal-','-handler.js'], ['',''], $file) . '-handler';
        $ver    = tpb_qv_ver(filemtime($path));
        wp_enqueue_script(
            $handle,
            TPB_QV_PLUGIN_URL . $rel,
            ['jquery', 'tpb-modal-state'],
            $ver,
            true
        );
        $asset_versions['scripts'][$handle] = $ver;
    }

    // NOTE: We intentionally DO NOT enqueue any cart “z-fix” script.
    // The chosen long-term solution is CSS-only context-aware stacking in modal.css.
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_assets', 20);

// -----------------------------------------------------------------------------
// Late neutralizer (belt & suspenders). Remove known stale handles re-added late
// -----------------------------------------------------------------------------
function tpb_qv_late_neutralizer() {
    $stale_scripts = [
        'tpb-qv-cart-portal',
        'tpb-qv-fix-cart-z',
        'tpb-qv-native',
        'tpb-qv-native-category',
        'tpb-qv-native-quickcheckout',
        'tpb-cart-fix',
    ];
    foreach ($stale_scripts as $h) {
        wp_dequeue_script($h);
        wp_deregister_script($h);
    }
}
add_action('wp_enqueue_scripts', 'tpb_qv_late_neutralizer', 999);

function tpb_qv_late_style_neutralizer() {
    $stale_styles = [
        'tpb-qv-modal-css-legacy',
        'tpb-qv-cart-fix-css',
        'tpb-qv-overlay-css',
    ];
    foreach ($stale_styles as $h) {
        wp_dequeue_style($h);
        wp_deregister_style($h);
    }
}
add_action('wp_enqueue_scripts', 'tpb_qv_late_style_neutralizer', 999);


// -----------------------------------------------------------------------------
// Footer-injected modal HTML (with a11y attributes)
// -----------------------------------------------------------------------------
function tpb_qv_add_modal_html() {
    if (is_admin()) return; ?>
    <!-- TPB Individual Modals v2.4 (a11y ready) -->

    <!-- Flower Station Modal -->
    <div id="tpb-qv-flower-modal" class="tpb-qv-overlay" aria-hidden="true">
        <div class="tpb-qv-modal" role="dialog" aria-modal="true" aria-label="Flower Station Quick View" data-modal-type="flower-station" tabindex="-1">
            <button class="tpb-qv-close" aria-label="Close modal" type="button">&times;</button>
            <?php include TPB_QV_PLUGIN_PATH . 'templates/modal-flower-stations.php'; ?>
        </div>
    </div>

    <!-- Category Station Modal -->
    <div id="tpb-qv-category-modal" class="tpb-qv-overlay" aria-hidden="true">
        <div class="tpb-qv-modal" role="dialog" aria-modal="true" aria-label="Category Station Quick View" data-modal-type="category-station" tabindex="-1">
            <button class="tpb-qv-close" aria-label="Close modal" type="button">&times;</button>
            <?php include TPB_QV_PLUGIN_PATH . 'templates/modal-category-stations.php'; ?>
        </div>
    </div>

    <!-- Quick Checkout Modal -->
    <div id="tpb-qv-quickcheckout-modal" class="tpb-qv-overlay" aria-hidden="true">
        <div class="tpb-qv-modal" role="dialog" aria-modal="true" aria-label="Quick Checkout Quick View" data-modal-type="quickcheckout-station" tabindex="-1">
            <button class="tpb-qv-close" aria-label="Close modal" type="button">&times;</button>
            <?php include TPB_QV_PLUGIN_PATH . 'templates/modal-quickcheckout-stations.php'; ?>
        </div>
    </div>

    <!-- Menu Boards Modal -->
    <div id="tpb-qv-menu-boards-modal" class="tpb-qv-overlay" aria-hidden="true">
        <div class="tpb-qv-modal" role="dialog" aria-modal="true" aria-label="Menu Boards Quick View" data-modal-type="menu-board" tabindex="-1">
            <button class="tpb-qv-close" aria-label="Close modal" type="button">&times;</button>
            <?php include TPB_QV_PLUGIN_PATH . 'templates/modal-menu-boards.php'; ?>
        </div>
    </div>

    <!-- Branded Stations Modal -->
    <div id="tpb-qv-branded-modal" class="tpb-qv-overlay" aria-hidden="true">
        <div class="tpb-qv-modal" role="dialog" aria-modal="true" aria-label="Branded Stations Quick View" data-modal-type="branded-station" tabindex="-1">
            <button class="tpb-qv-close" aria-label="Close modal" type="button">&times;</button>
            <?php include TPB_QV_PLUGIN_PATH . 'templates/modal-branded-stations.php'; ?>
        </div>
    </div>
<?php }
add_action('wp_footer', 'tpb_qv_add_modal_html');

// -----------------------------------------------------------------------------
// Iframe mode: add body class + enqueue specific styles (single hook)
// -----------------------------------------------------------------------------
function tpb_qv_add_iframe_class($classes) {
    if (isset($_GET['tpb_qv_iframe']) && $_GET['tpb_qv_iframe'] === '1') $classes[] = 'tpb-qv-iframe';
    return $classes;
}
add_filter('body_class', 'tpb_qv_add_iframe_class');

function tpb_qv_enqueue_iframe_assets() {
    if (!isset($_GET['tpb_qv_iframe']) || $_GET['tpb_qv_iframe'] !== '1') return;

    $rel  = 'assets/css/iframe-content.css';
    $path = TPB_QV_PLUGIN_PATH . $rel;
    if (!file_exists($path)) return;

    // Only enqueue in iframe mode
    wp_enqueue_style('tpb-qv-iframe-content', TPB_QV_PLUGIN_URL . $rel, [], tpb_qv_ver(filemtime($path)));
    wp_add_inline_style(
        'tpb-qv-iframe-content',
        'body.tpb-qv-iframe .tpb-hidden{display:none !important;} body.tpb-qv-iframe form.cart .tpb-hidden{display:none !important;}'
    );
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_iframe_assets', 5);

// -----------------------------------------------------------------------------
// CPB Integration Hooks
// -----------------------------------------------------------------------------
function tpb_qv_cpb_integration() {
    if (!class_exists('Addify_Composite_Product')) return;

    add_action('woocommerce_single_product_summary', 'tpb_qv_add_cpb_modal_trigger', 25);
    add_action('wp_ajax_tpb_qv_cpb_add_to_cart',        'tpb_qv_handle_cpb_add_to_cart');
    add_action('wp_ajax_nopriv_tpb_qv_cpb_add_to_cart', 'tpb_qv_handle_cpb_add_to_cart');
}
add_action('init', 'tpb_qv_cpb_integration');

/**
 * Add modal trigger to CPB buttons (progressive enhancement, idempotent)
 * NOTE: Handlers now require a typed modal trigger. Default is filterable.
 */
function tpb_qv_add_cpb_modal_trigger() {
    global $product;
    if (!$product || !$product->is_type('composite')) return;

    $modal_type = apply_filters('tpb_qv_cpb_default_modal_type', 'category-station');
    $product_id = (int) $product->get_id(); ?>
    <script>
    jQuery(function($){
      $('.single_add_to_cart_button, .afc_add_to_cart_button').each(function(){
        const $btn = $(this);
        if ($btn.data('tpbModalWired')) return;

        // Stamp a typed trigger so the narrowed handlers can catch it
        $btn.attr('data-tpb-modal', <?php echo json_encode($modal_type); ?>);
        $btn.attr('data-product-id', <?php echo (int) $product_id; ?>);
        $btn.data('tpbModalWired', true);

        // Optional: preserve URL (may be used by handlers if desired)
        const href = $btn.attr('href') || window.location.href;
        $btn.attr('data-product-url', href);

        if (window.TPB_QV_CONFIG && TPB_QV_CONFIG.debug) {
          console.log('[TPB] CPB button wired for modal:', ($btn.text() || '').trim(), '→', <?php echo json_encode($modal_type); ?>, 'pid=', <?php echo (int) $product_id; ?>);
        }
      });
    });
    </script>
<?php }

/**
 * Handle CPB add to cart from iframe
 */
function tpb_qv_handle_cpb_add_to_cart() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'tpb_qv_nonce')) wp_die('Security check failed');

    $product_id   = (int)($_POST['product_id'] ?? 0);
    $quantity     = max(1, (int)($_POST['quantity'] ?? 1));
    $variation_id = (int)($_POST['variation_id'] ?? 0);

    if (!$product_id) wp_send_json_error('Invalid product ID');

    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
    if ($cart_item_key) {
        wp_send_json_success([
            'message'  => 'Product added to cart successfully',
            'cart_url' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart'),
        ]);
    } else {
        wp_send_json_error('Failed to add product to cart');
    }
}

// -----------------------------------------------------------------------------
// AJAX: get product image (robust: featured → gallery → post thumbnail → filter)
// -----------------------------------------------------------------------------
add_action('wp_ajax_tpb_qv_get_product_image',        'tpb_qv_get_product_image');
add_action('wp_ajax_nopriv_tpb_qv_get_product_image', 'tpb_qv_get_product_image');
function tpb_qv_get_product_image() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'tpb_qv_nonce')) {
        wp_send_json_error(['message' => 'bad_nonce'], 403);
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    if (!$product_id) {
        wp_send_json_error(['message' => 'missing_product_id'], 400);
    }

    $image_id = 0;

    // Prefer Woo featured image / gallery if available
    if (function_exists('wc_get_product')) {
        $product = wc_get_product($product_id);
        if ($product) {
            $image_id = (int) $product->get_image_id();
            if (!$image_id) {
                $gallery_ids = $product->get_gallery_image_ids();
                if (!empty($gallery_ids)) {
                    $image_id = (int) $gallery_ids[0];
                }
            }
        }
    }

    // Fallback to post thumbnail
    if (!$image_id) {
        $thumb = (int) get_post_thumbnail_id($product_id);
        if ($thumb) $image_id = $thumb;
    }

    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
    $image_alt = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';

    // Final filter-based fallback (theme/plugin can inject a URL)
    if (!$image_url) {
        $image_url = apply_filters('tpb_qv_image_fallback_url', '', $product_id);
    }

    if ($image_url) {
        wp_send_json_success([
            'image_url' => esc_url_raw($image_url),
            'image_alt' => $image_alt ? sanitize_text_field($image_alt) : 'Product image',
        ]);
    } else {
        wp_send_json_error(['message' => 'no_image_found'], 404);
    }
}

// -----------------------------------------------------------------------------
// AJAX: get products for stepper (public read; sanitize inputs)
// -----------------------------------------------------------------------------
add_action('wp_ajax_tpb_qv_products',        'tpb_qv_get_products');
add_action('wp_ajax_nopriv_tpb_qv_products', 'tpb_qv_get_products');
function tpb_qv_get_products() {
    $tags_param       = isset($_GET['tags']) ? sanitize_text_field(wp_unslash($_GET['tags'])) : '';
    $cats_param       = isset($_GET['categories']) ? sanitize_text_field(wp_unslash($_GET['categories'])) : '';
    $limit_param      = isset($_GET['limit']) ? (int) $_GET['limit'] : 24;

    $tags       = $tags_param ? array_filter(array_map('sanitize_title', array_map('trim', explode(',', $tags_param)))) : [];
    $categories = $cats_param ? array_filter(array_map('sanitize_title', array_map('trim', explode(',', $cats_param)))) : [];
    $limit      = min(max($limit_param, 1), 60);

    $args = ['limit' => $limit, 'status' => 'publish'];
    if (!empty($tags))       $args['tag']      = $tags;       // Woo supports slugs array
    if (!empty($categories)) $args['category'] = $categories; // Woo supports slugs array

    $products = function_exists('wc_get_products') ? wc_get_products($args) : [];

    $results = array_map(function($product) {
        if (!is_a($product, 'WC_Product')) return null;
        $id   = $product->get_id();
        $cats = get_the_terms($id, 'product_cat') ?: [];
        $tags = get_the_terms($id, 'product_tag') ?: [];
        return [
            'id'         => $id,
            'name'       => $product->get_name(),
            'slug'       => $product->get_slug(),
            'price_html' => $product->get_price_html(),
            'image_url'  => $product->get_image_id() ? wp_get_attachment_image_url($product->get_image_id(), 'full') : '',
            'categories' => array_values(array_map(function($cat){ return ['slug'=>$cat->slug,'name'=>$cat->name]; }, array_filter($cats))),
            'tags'       => array_values(array_map(function($tag){ return ['slug'=>$tag->slug,'name'=>$tag->name]; }, array_filter($tags))),
        ];
    }, $products);

    $results = array_values(array_filter($results));
    wp_send_json_success(['results' => $results]);
}
