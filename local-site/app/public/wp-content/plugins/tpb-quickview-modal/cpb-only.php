<?php
/**
 * CPB Only View for Modal
 * This file shows only the CPB configuration
 */

// Check if this is a CPB-only request
if (!isset($_GET['tpb_qv_cpb_only']) || $_GET['tpb_qv_cpb_only'] !== '1') {
    return;
}

// Load WordPress
require_once('../../../wp-load.php');

// Get the product ID from URL
$product_id = get_the_ID();
if (!$product_id) {
    // Try to get from URL parameter
    $product_slug = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $product = get_page_by_path($product_slug, OBJECT, 'product');
    if ($product) {
        $product_id = $product->ID;
    }
}

if (!$product_id) {
    echo '<div style="padding: 20px; text-align: center;">Product not found</div>';
    exit;
}

// Set up the product
global $product;
$product = wc_get_product($product_id);

if (!$product) {
    echo '<div style="padding: 20px; text-align: center;">Product not found</div>';
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configure Product</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #fff;
        }
        .cpb-container {
            max-width: 100%;
        }
        /* Hide everything except CPB */
        .woocommerce-product-gallery,
        .woocommerce-product-details__short-description,
        .woocommerce-product-details__short-description p,
        .woocommerce-product-meta,
        .woocommerce-product-details__short-description p:not(.afc-composite-product),
        .woocommerce-product-details__short-description > p:first-child {
            display: none !important;
        }
        /* Show only CPB content */
        .afc-composite-product,
        .afc-composite-product *,
        .woocommerce-product-details__short-description .afc-composite-product {
            display: block !important;
        }
    </style>
</head>
<body>
    <div class="cpb-container">
        <?php
        // Show only the CPB content
        if (class_exists('Addify_Composite_Product')) {
            // Get the CPB content
            ob_start();
            do_action('woocommerce_single_product_summary');
            $content = ob_get_clean();
            
            // Extract only CPB-related content
            if (strpos($content, 'afc-composite-product') !== false) {
                echo $content;
            } else {
                // Fallback: show product title and CPB if available
                echo '<h2>' . get_the_title($product_id) . '</h2>';
                echo '<div class="afc-composite-product">';
                echo '<p>CPB Configuration will appear here</p>';
                echo '</div>';
            }
        } else {
            echo '<div style="padding: 20px; text-align: center;">CPB Plugin not available</div>';
        }
        ?>
    </div>
</body>
</html>







