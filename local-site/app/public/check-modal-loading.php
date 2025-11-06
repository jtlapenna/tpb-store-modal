<?php
/**
 * Check if Modal System is Loading on Product Pages
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-includes/wp-db.php');

echo "<h1>🔍 Modal System Loading Check</h1>";

// Check if plugin is active
$plugin_file = 'tpb-quickview-modal/tpb-quickview-modal.php';
$is_active = is_plugin_active($plugin_file);
echo "<h2>Plugin Status:</h2>";
echo "<p><strong>TPB Quickview Modal:</strong> " . ($is_active ? '✅ Active' : '❌ Inactive') . "</p>";

if (!$is_active) {
    echo "<p>❌ Plugin is not active. Please activate it in WordPress admin.</p>";
    exit;
}

// Check if assets are being enqueued
echo "<h2>Asset Files:</h2>";
$plugin_path = WP_PLUGIN_DIR . '/tpb-quickview-modal/tpb-quickview-modal/';
$js_file = $plugin_path . 'assets/js/modal.js';
$css_file = $plugin_path . 'assets/css/modal.css';

echo "<p><strong>JavaScript:</strong> " . (file_exists($js_file) ? '✅ Exists' : '❌ Missing') . "</p>";
echo "<p><strong>CSS:</strong> " . (file_exists($css_file) ? '✅ Exists' : '❌ Missing') . "</p>";

// Check if functions are defined
echo "<h2>Function Status:</h2>";
echo "<p><strong>tpb_qv_enqueue_assets:</strong> " . (function_exists('tpb_qv_enqueue_assets') ? '✅ Defined' : '❌ Not defined') . "</p>";
echo "<p><strong>tpb_qv_cpb_integration:</strong> " . (function_exists('tpb_qv_cpb_integration') ? '✅ Defined' : '❌ Not defined') . "</p>";

// Test product page
$product_slug = 'flower-station-configure-now';
$product = get_page_by_path($product_slug, OBJECT, 'product');

if ($product) {
    echo "<h2>Product Test:</h2>";
    echo "<p><strong>Product:</strong> {$product->post_title} (ID: {$product->ID})</p>";
    
    $product_url = get_permalink($product->ID);
    echo "<p><strong>Product URL:</strong> <a href='{$product_url}' target='_blank'>{$product_url}</a></p>";
    
    // Check if it's a composite product
    $is_composite = get_post_meta($product->ID, '_afc_composite_product', true);
    echo "<p><strong>Is CPB Product:</strong> " . ($is_composite ? '✅ Yes' : '❌ No') . "</p>";
    
    // Check CPB plugin
    echo "<p><strong>CPB Plugin:</strong> " . (class_exists('Addify_Composite_Product') ? '✅ Active' : '❌ Inactive') . "</p>";
}

echo "<h2>🧪 Test Instructions:</h2>";
echo "<ol>";
echo "<li>Go to the product page: <a href='{$product_url}' target='_blank'>{$product_url}</a></li>";
echo "<li>Open browser console (F12)</li>";
echo "<li>Look for these messages:</li>";
echo "<ul>";
echo "<li>🚀 TPB QuickView Modal Plugin Loading...</li>";
echo "<li>🔍 Detecting Configure Now buttons...</li>";
echo "<li>✅ CPB button wired for modal:</li>";
echo "</ul>";
echo "<li>Look for buttons with 'Configure', 'Customize', or 'Build' text</li>";
echo "<li>Click them to test modal opening</li>";
echo "</ol>";

echo "<h2>🔧 Quick Fix for Staging Page:</h2>";
echo "<p>If the Configure Now button on your staging page isn't working:</p>";
echo "<ol>";
echo "<li>Go to your staging page</li>";
echo "<li>Open browser console (F12)</li>";
echo "<li>Paste this code and press Enter:</li>";
echo "</ol>";
echo "<pre>";
echo "// Fix Configure Now buttons
const buttons = document.querySelectorAll('a[href*=\"/product/\"]');
buttons.forEach(btn => {
    if (btn.textContent.toLowerCase().includes('configure')) {
        btn.setAttribute('data-tpb-modal', 'true');
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof window.TPBModal !== 'undefined') {
                window.TPBModal.open(this.href);
            }
        });
        console.log('✅ Fixed button:', btn.textContent.trim());
    }
});";
echo "</pre>";
?>







