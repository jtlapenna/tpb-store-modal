<?php
/**
 * Check Product CPB Configuration
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-includes/wp-db.php');

// Get the product
$product_id = 0;
$product_slug = 'flower-station-configure-now';

// Find product by slug
$product = get_page_by_path($product_slug, OBJECT, 'product');
if ($product) {
    $product_id = $product->ID;
    echo "<h2>Product Found: {$product->post_title} (ID: {$product_id})</h2>";
} else {
    echo "<h2>❌ Product not found</h2>";
    exit;
}

// Check product meta
echo "<h3>Product Meta Data:</h3>";
$meta = get_post_meta($product_id);
foreach ($meta as $key => $value) {
    if (strpos($key, 'afc_') === 0 || strpos($key, '_afc_') === 0) {
        echo "<strong>{$key}:</strong> " . (is_array($value) ? implode(', ', $value) : $value) . "<br>";
    }
}

// Check if it's a composite product
$is_composite = get_post_meta($product_id, '_afc_composite_product', true);
echo "<h3>CPB Status:</h3>";
echo "<strong>Is Composite Product:</strong> " . ($is_composite ? '✅ Yes' : '❌ No') . "<br>";

// Check product type
$product_type = get_post_meta($product_id, '_product_type', true);
echo "<strong>Product Type:</strong> " . ($product_type ?: 'Not set') . "<br>";

// Check if CPB plugin is active
echo "<h3>Plugin Status:</h3>";
echo "<strong>CPB Plugin Active:</strong> " . (class_exists('Addify_Composite_Product') ? '✅ Yes' : '❌ No') . "<br>";

// Check for CPB components
$components = get_post_meta($product_id, '_afc_composite_products', true);
echo "<strong>CPB Components:</strong> " . (is_array($components) ? count($components) . ' components' : 'None') . "<br>";

if (is_array($components) && !empty($components)) {
    echo "<h4>Components:</h4>";
    foreach ($components as $i => $component) {
        echo "Component {$i}: " . print_r($component, true) . "<br>";
    }
}

echo "<h3>All Meta Keys:</h3>";
echo "<pre>";
foreach ($meta as $key => $value) {
    echo "{$key}: " . (is_array($value) ? print_r($value, true) : $value) . "\n";
}
echo "</pre>";
?>







