<?php
/**
 * TPB Modal System Test Script
 * This script helps test the modal functionality
 */

// Load WordPress
require_once('/Users/jeff/Projects/tpb-store-modal/local-site/app/public/wp-config.php');
require_once('/Users/jeff/Projects/tpb-store-modal/local-site/app/public/wp-includes/wp-db.php');

echo "<h1>🧪 TPB Modal System Test</h1>";

// Check if plugins are active
echo "<h2>Plugin Status</h2>";
echo "<ul>";
echo "<li><strong>TPB Quickview Modal:</strong> " . (is_plugin_active('tpb-quickview-modal/tpb-quickview-modal.php') ? '✅ Active' : '❌ Inactive') . "</li>";
echo "<li><strong>Addify CPB:</strong> " . (is_plugin_active('addify-configurable-product-builder/addify-composite-product.php') ? '✅ Active' : '❌ Inactive') . "</li>";
echo "<li><strong>WooCommerce:</strong> " . (is_plugin_active('woocommerce/woocommerce.php') ? '✅ Active' : '❌ Inactive') . "</li>";
echo "</ul>";

// Check for CPB products
echo "<h2>CPB Products Found</h2>";
$cpb_products = get_posts([
    'post_type' => 'product',
    'meta_query' => [
        [
            'key' => '_afc_composite_product',
            'value' => 'yes',
            'compare' => '='
        ]
    ],
    'posts_per_page' => 5
]);

if (empty($cpb_products)) {
    echo "<p>❌ No CPB products found. You may need to create some test products.</p>";
} else {
    echo "<p>✅ Found " . count($cpb_products) . " CPB products:</p>";
    echo "<ul>";
    foreach ($cpb_products as $product) {
        $product_url = get_permalink($product->ID);
        echo "<li><a href='{$product_url}' target='_blank'>{$product->post_title}</a> (ID: {$product->ID})</li>";
    }
    echo "</ul>";
}

// Check for regular products
echo "<h2>Regular Products Found</h2>";
$regular_products = get_posts([
    'post_type' => 'product',
    'posts_per_page' => 5
]);

if (empty($regular_products)) {
    echo "<p>❌ No products found.</p>";
} else {
    echo "<p>✅ Found " . count($regular_products) . " products:</p>";
    echo "<ul>";
    foreach ($regular_products as $product) {
        $product_url = get_permalink($product->ID);
        echo "<li><a href='{$product_url}' target='_blank'>{$product->post_title}</a> (ID: {$product->ID})</li>";
    }
    echo "</ul>";
}

// Test modal configuration
echo "<h2>Modal Configuration</h2>";
echo "<ul>";
echo "<li><strong>Plugin URL:</strong> " . (defined('TPB_QV_PLUGIN_URL') ? TPB_QV_PLUGIN_URL : 'Not defined') . "</li>";
echo "<li><strong>Plugin Path:</strong> " . (defined('TPB_QV_PLUGIN_PATH') ? TPB_QV_PLUGIN_PATH : 'Not defined') . "</li>";
echo "<li><strong>Debug Mode:</strong> " . (defined('WP_DEBUG') && WP_DEBUG ? '✅ Enabled' : '❌ Disabled') . "</li>";
echo "</ul>";

// Test JavaScript and CSS files
echo "<h2>Asset Files</h2>";
$js_file = '/Users/jeff/Projects/tpb-store-modal/local-site/app/public/wp-content/plugins/tpb-quickview-modal/tpb-quickview-modal/assets/js/modal.js';
$css_file = '/Users/jeff/Projects/tpb-store-modal/local-site/app/public/wp-content/plugins/tpb-quickview-modal/tpb-quickview-modal/assets/css/modal.css';

echo "<ul>";
echo "<li><strong>JavaScript:</strong> " . (file_exists($js_file) ? '✅ Exists' : '❌ Missing') . "</li>";
echo "<li><strong>CSS:</strong> " . (file_exists($css_file) ? '✅ Exists' : '❌ Missing') . "</li>";
echo "</ul>";

// Test instructions
echo "<h2>🧪 How to Test</h2>";
echo "<ol>";
echo "<li><strong>Go to a product page</strong> with CPB functionality</li>";
echo "<li><strong>Look for buttons</strong> with text like 'Configure', 'Customize', or 'Build'</li>";
echo "<li><strong>Click the button</strong> - it should open in a modal</li>";
echo "<li><strong>Check browser console</strong> for debug messages</li>";
echo "<li><strong>Test form submission</strong> within the modal</li>";
echo "</ol>";

echo "<h2>🔧 Debug Information</h2>";
echo "<p>Open browser console (F12) and look for messages starting with:</p>";
echo "<ul>";
echo "<li>🚀 TPB QuickView Modal Plugin Loading...</li>";
echo "<li>🔍 Detecting Configure Now buttons...</li>";
echo "<li>✅ CPB button wired for modal:</li>";
echo "<li>🔴 Modal trigger clicked:</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after testing for security!</p>";
?>
