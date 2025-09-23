<?php
/**
 * TPB Quick View Modal Functions
 * Child theme functions for the TPB Store Modal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue modal assets
 */
function tpb_qv_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style(
        'tpb-qv-css',
        get_stylesheet_directory_uri() . '/assets/css/tpb-qv.css',
        [],
        filemtime(get_stylesheet_directory() . '/assets/css/tpb-qv.css')
    );
    
    // Enqueue parent window JS
    wp_enqueue_script(
        'tpb-modal-js',
        get_stylesheet_directory_uri() . '/assets/js/tpb-modal.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/assets/js/tpb-modal.js'),
        true
    );
    
    // Enqueue iframe JS
    wp_enqueue_script(
        'tpb-qv-iframe-js',
        get_stylesheet_directory_uri() . '/assets/js/tpb-qv-iframe.js',
        [],
        filemtime(get_stylesheet_directory() . '/assets/js/tpb-qv-iframe.js'),
        true
    );
    
    // Localize script with configuration
    wp_localize_script('tpb-modal-js', 'TPB_QV_CFG', [
        'home_url' => home_url(),
        'qv_param' => 'tpb_qv'
    ]);
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_assets');

/**
 * Add body class for quick view mode
 */
function tpb_qv_body_class($classes) {
    if (isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') {
        $classes[] = 'tpb-qv';
    }
    return $classes;
}
add_filter('body_class', 'tpb_qv_body_class');

/**
 * Hide header/footer in iframe mode
 */
add_action('wp_head', function() {
    if (isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') {
        echo '<style>
            .tpb-qv .site-header,
            .tpb-qv .site-footer,
            .tpb-qv #wpadminbar,
            .tpb-qv .woocommerce-breadcrumb {
                display: none !important;
            }
            .tpb-qv body {
                display: flex !important;
                height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .tpb-qv .tpb-qv-left-panel {
                width: 45% !important;
                height: 100vh !important;
                flex-shrink: 0 !important;
                box-sizing: border-box !important;
            }
            .tpb-qv .tpb-qv-right-panel {
                width: 55% !important;
                flex: 1 !important;
                height: 100vh !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .tpb-qv .tpb-hidden { display: none !important; }
        </style>';
    }
});

/**
 * Simple iframe setup for quick view
 */
add_action('wp_footer', function() {
    if (isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const body = document.body;
            const product = document.querySelector(".woocommerce div.product");
            
            if (product) {
                // Create panels
                const leftPanel = document.createElement("div");
                leftPanel.className = "tpb-qv-left-panel";
                
                const rightPanel = document.createElement("div");
                rightPanel.className = "tpb-qv-right-panel";
                
                // Move gallery to left panel
                const gallery = product.querySelector(".woocommerce-product-gallery");
                if (gallery) {
                    leftPanel.appendChild(gallery.cloneNode(true));
                }
                
                // Move summary to right panel
                const summary = product.querySelector(".summary");
                if (summary) {
                    rightPanel.appendChild(summary.cloneNode(true));
                }
                
                // Clear body and add panels
                body.innerHTML = "";
                body.appendChild(leftPanel);
                body.appendChild(rightPanel);
            }
        });
        </script>';
    }
});

/**
 * Convenience shortcode for adding a Quick View trigger button anywhere:
 * Usage: [tpb_qv_button product="4607" label="Configure Now"]
 */
add_shortcode('tpb_qv_button', function($atts = []) {
    $atts = shortcode_atts([
        'product' => '',
        'label'   => 'Configure Now',
        'class'   => '',
    ], $atts, 'tpb_qv_button');

    $product_id = absint($atts['product']);
    if (!$product_id) return '';

    $url = get_permalink($product_id);
    if (!$url) return '';

    $classes = trim('tpb-qv-trigger button ' . $atts['class']);
    $label   = esc_html($atts['label']);
    $url     = esc_url($url);

    return sprintf(
        '<a class="%1$s" href="%2$s" data-product-url="%2$s">%3$s</a>',
        esc_attr($classes),
        $url,
        $label
    );
});