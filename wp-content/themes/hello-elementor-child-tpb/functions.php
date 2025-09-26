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
        get_stylesheet_directory_uri() . '/assets/js/tpb-qv-iframe.js?v=' . time() . '&r=' . rand(1000, 9999),
        [],
        time(), // Force cache bust
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
    if ((isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') || (isset($_GET['tpb_qv_staging']) && $_GET['tpb_qv_staging'] == '1')) {
        $classes[] = 'tpb-qv';
    }
    return $classes;
}
add_filter('body_class', 'tpb_qv_body_class');

/**
 * Hide header/footer in iframe mode
 */
add_action('wp_head', function() {
    if ((isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') || (isset($_GET['tpb_qv_staging']) && $_GET['tpb_qv_staging'] == '1')) {
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
    // Always log for debugging
    error_log('TPB DEBUG: wp_footer action triggered');
    error_log('TPB DEBUG: tpb_qv = ' . (isset($_GET['tpb_qv']) ? $_GET['tpb_qv'] : 'not set'));
    error_log('TPB DEBUG: tpb_qv_staging = ' . (isset($_GET['tpb_qv_staging']) ? $_GET['tpb_qv_staging'] : 'not set'));
    
    // Always output a test script to verify wp_footer is working
    echo '<!-- TPB TEST: wp_footer is working -->';
    echo '<script>console.log("🧪 TPB TEST: wp_footer script loaded");</script>';
    
    if ((isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') || (isset($_GET['tpb_qv_staging']) && $_GET['tpb_qv_staging'] == '1')) {
        error_log('TPB DEBUG: Iframe setup condition met, outputting script');
        echo '<!-- TPB IFRAME SETUP SCRIPT LOADING -->';
        echo '<script>
        console.log("🚀 TPB IFRAME SETUP: Script loaded and executing!");
        console.log("🚀 TPB IFRAME SETUP: URL params - tpb_qv:", "' . (isset($_GET['tpb_qv']) ? $_GET['tpb_qv'] : 'not set') . '", "tpb_qv_staging:", "' . (isset($_GET['tpb_qv_staging']) ? $_GET['tpb_qv_staging'] : 'not set') . '");
        document.addEventListener("DOMContentLoaded", function() {
            console.log("🔍 IFRAME SETUP: Starting iframe content analysis...");
            
            const body = document.body;
            const product = document.querySelector(".woocommerce div.product");
            
            // Enhanced debugging
            console.log("🔍 IFRAME SETUP: Body HTML length:", body.innerHTML.length);
            console.log("🔍 IFRAME SETUP: All .woocommerce elements:", document.querySelectorAll(".woocommerce").length);
            console.log("🔍 IFRAME SETUP: All .product elements:", document.querySelectorAll(".product").length);
            console.log("🔍 IFRAME SETUP: All .single-product elements:", document.querySelectorAll(".single-product").length);
            console.log("🔍 IFRAME SETUP: All .woocommerce-product elements:", document.querySelectorAll(".woocommerce-product").length);
            
            // Check for any product-related content
            const productContent = document.querySelector(".woocommerce-product, .product, .single-product, .woocommerce");
            console.log("🔍 IFRAME SETUP: Product content found:", !!productContent);
            
            if (productContent) {
                console.log("🔍 IFRAME SETUP: Product content HTML preview:", productContent.innerHTML.substring(0, 500) + "...");
            }
            
            if (product) {
                console.log("🔍 IFRAME SETUP: Product element found, setting up panels...");
                
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
                
                // Also move any CPB components that might be outside summary
                const cpbComponents = product.querySelectorAll(".af_cp_all_components_content, .woocommerce-variation, form.cart");
                cpbComponents.forEach(comp => {
                    if (!rightPanel.contains(comp)) {
                        rightPanel.appendChild(comp.cloneNode(true));
                    }
                });
                
                // Debug: Log what we found
                console.log("🔍 Gallery found:", !!gallery);
                console.log("🔍 Summary found:", !!summary);
                console.log("🔍 CPB components found:", cpbComponents.length);
                console.log("🔍 Right panel content:", rightPanel.innerHTML.substring(0, 200) + "...");
                
                // Additional debugging for CPB
                const allForms = document.querySelectorAll('form');
                const allSelects = document.querySelectorAll('select');
                const allVariations = document.querySelectorAll('.variations');
                console.log("🔍 All forms found:", allForms.length);
                console.log("🔍 All selects found:", allSelects.length);
                console.log("🔍 All variations found:", allVariations.length);
                
                // Check for Addify CPB specifically
                const addifyCPB = document.querySelectorAll('.af_cp_all_components_content');
                console.log("🔍 Addify CPB found:", addifyCPB.length);
                
                // Check for WooCommerce variations
                const wcVariations = document.querySelectorAll('.woocommerce-variation');
                console.log("🔍 WooCommerce variations found:", wcVariations.length);
                
                // Clear body and add panels
                body.innerHTML = "";
                body.appendChild(leftPanel);
                body.appendChild(rightPanel);
                
                // Wait for CPB components to load if they're not immediately available
                if (cpbComponents.length === 0) {
                    console.log("⏳ No CPB components found immediately, waiting for them to load...");
                    
                    const checkForCPB = setInterval(() => {
                        const newCPBComponents = document.querySelectorAll(".af_cp_all_components_content, .woocommerce-variation, form.cart");
                        console.log("⏳ Checking for CPB components again, found:", newCPBComponents.length);
                        
                        if (newCPBComponents.length > 0) {
                            console.log("✅ CPB components loaded, adding to right panel");
                            newCPBComponents.forEach(comp => {
                                if (!rightPanel.contains(comp)) {
                                    rightPanel.appendChild(comp.cloneNode(true));
                                }
                            });
                            clearInterval(checkForCPB);
                        }
                    }, 500);
                    
                    // Stop checking after 10 seconds
                    setTimeout(() => {
                        clearInterval(checkForCPB);
                        console.log("⏰ Stopped waiting for CPB components");
                    }, 10000);
                }
            } else {
                console.log("❌ No product found on page - this is the problem!");
                console.log("🔍 IFRAME SETUP: Page content analysis:");
                console.log("  - Body classes:", body.className);
                console.log("  - Page title:", document.title);
                console.log("  - All elements with \'product\' in class:", document.querySelectorAll("[class*=\'product\']").length);
                console.log("  - All elements with \'woocommerce\' in class:", document.querySelectorAll("[class*=\'woocommerce\']").length);
                console.log("  - All main content areas:", document.querySelectorAll("main, .content, .site-content, .entry-content").length);
                
                // Show the actual page content
                console.log("🔍 IFRAME SETUP: Full body HTML:", body.innerHTML.substring(0, 1000) + "...");
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