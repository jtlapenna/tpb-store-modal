<?php
/**
 * MU QuickView Utilities
 * Purpose: Provide REST endpoints and deploy cache flush. Do NOT enqueue front-end assets.
 */

if (!defined('ABSPATH')) exit;

/**
 * Constants
 */
if (!defined('TPB_QV_MU_DIR')) define('TPB_QV_MU_DIR', __DIR__);
if (!defined('TPB_QV_MU_URL')) define('TPB_QV_MU_URL', (function () {
    // Best effort URL from wp-content URL + mu-plugins
    $content_url = content_url();
    return $content_url . '/mu-plugins/tpb-quickview';
})());

/**
 * REST: /wp-json/tpb/v1/qv/products
 * (Ported from prior MU file—adjust response shape as needed)
 */
add_action('rest_api_init', function () {
    register_rest_route('tpb/v1', '/qv/products', [
        'methods'  => 'GET',
        'callback' => function (\WP_REST_Request $req) {
            $tags       = array_filter(array_map('sanitize_text_field', explode(',', (string)$req->get_param('tags'))));
            $categories = array_filter(array_map('sanitize_text_field', explode(',', (string)$req->get_param('categories'))));
            $limit      = max(1, min(48, (int)$req->get_param('limit') ?: 24));

            $args = [
                'limit'  => $limit,
                'status' => 'publish',
            ];
            if ($tags)       $args['tag']      = $tags;
            if ($categories) $args['category'] = $categories;

            if (!function_exists('wc_get_products')) {
                return new \WP_REST_Response(['results' => []], 200);
            }

            $products = wc_get_products($args);

            $results = array_map(function ($product) {
                return [
                    'id'         => $product->get_id(),
                    'name'       => $product->get_name(),
                    'slug'       => $product->get_slug(),
                    'price_html' => $product->get_price_html(),
                    'image_url'  => wp_get_attachment_image_url($product->get_image_id(), 'full'),
                    'categories' => array_map(function ($cat) {
                        return ['slug' => $cat->slug, 'name' => $cat->name];
                    }, get_the_terms($product->get_id(), 'product_cat') ?: []),
                    'tags' => array_map(function ($tag) {
                        return ['slug' => $tag->slug, 'name' => $tag->name];
                    }, get_the_terms($product->get_id(), 'product_tag') ?: []),
                ];
            }, $products);

            return new \WP_REST_Response(['results' => $results], 200);
        },
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Deploy flush endpoint
 * Hit: /?tpb_deploy_flush=YOUR_TOKEN
 * Add your own token check / IP allow-listing as needed.
 */
add_action('init', function () {
    if (!isset($_GET['tpb_deploy_flush'])) return;

    // Optional: basic shared secret guard
    $expected = defined('TPB_QV_DEPLOY_TOKEN') ? TPB_QV_DEPLOY_TOKEN : null;
    if ($expected && $_GET['tpb_deploy_flush'] !== $expected) {
        status_header(403);
        exit('Forbidden');
    }

    // Clear common caches safely
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }
    if (class_exists('\Elementor\Plugin')) {
        try { \Elementor\Plugin::$instance->files_manager->clear_cache(); } catch (\Throwable $e) {}
        try { \Elementor\Plugin::$instance->frontend->clear_cache(); } catch (\Throwable $e) {}
    }
    // Autoptimize
    if (function_exists('autoptimizeCache::clearall')) {
        try { \autoptimizeCache::clearall(); } catch (\Throwable $e) {}
    }

    wp_die('TPB deploy flush complete');
});

/**
 * (Optional) Register—do not enqueue—legacy scripts for backstop use
 * Your main plugin should enqueue its own canonical versions.
 */
add_action('wp_enqueue_scripts', function () {
    // Register (no enqueue!) only if files exist—just as a fallback
    $assets = [
        'tpb-modal-state' => 'assets/js/modal-state.js',
    ];
    foreach ($assets as $handle => $rel) {
        $path = TPB_QV_MU_DIR . '/' . $rel;
        if (file_exists($path)) {
            wp_register_script(
                $handle,
                TPB_QV_MU_URL . '/' . $rel,
                ['jquery'],
                filemtime($path),
                true
            );
        }
    }
}, 1);

/**
 * Utilities & other MU helpers
 * (If you had preloaders or small admin-only utilities, keep them here;
 * make sure none of them enqueues front-end assets.)
 */
