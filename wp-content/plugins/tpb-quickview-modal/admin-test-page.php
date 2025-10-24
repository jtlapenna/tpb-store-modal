<?php
/**
 * TPB Modal System Test Page
 * Add this to WordPress admin for testing
 */

// Add admin menu
add_action('admin_menu', 'tpb_qv_add_test_page');

function tpb_qv_add_test_page() {
    add_management_page(
        'TPB Modal Test',
        'TPB Modal Test',
        'manage_options',
        'tpb-modal-test',
        'tpb_qv_test_page_content'
    );
}

function tpb_qv_test_page_content() {
    ?>
    <div class="wrap">
        <h1>🧪 TPB Modal System Test</h1>
        
        <h2>Plugin Status</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Plugin</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>TPB Quickview Modal</td>
                    <td><?php echo is_plugin_active('tpb-quickview-modal/tpb-quickview-modal.php') ? '✅ Active' : '❌ Inactive'; ?></td>
                </tr>
                <tr>
                    <td>Addify CPB</td>
                    <td><?php echo is_plugin_active('addify-configurable-product-builder/addify-composite-product.php') ? '✅ Active' : '❌ Inactive'; ?></td>
                </tr>
                <tr>
                    <td>WooCommerce</td>
                    <td><?php echo is_plugin_active('woocommerce/woocommerce.php') ? '✅ Active' : '❌ Inactive'; ?></td>
                </tr>
            </tbody>
        </table>
        
        <h2>CPB Products</h2>
        <?php
        $cpb_products = get_posts([
            'post_type' => 'product',
            'meta_query' => [
                [
                    'key' => '_afc_composite_product',
                    'value' => 'yes',
                    'compare' => '='
                ]
            ],
            'posts_per_page' => 10
        ]);
        
        if (empty($cpb_products)) {
            echo '<p>❌ No CPB products found. Create some test products first.</p>';
        } else {
            echo '<p>✅ Found ' . count($cpb_products) . ' CPB products:</p>';
            echo '<ul>';
            foreach ($cpb_products as $product) {
                $product_url = get_permalink($product->ID);
                echo '<li><a href="' . $product_url . '" target="_blank">' . $product->post_title . '</a> (ID: ' . $product->ID . ')</li>';
            }
            echo '</ul>';
        }
        ?>
        
        <h2>Regular Products</h2>
        <?php
        $regular_products = get_posts([
            'post_type' => 'product',
            'posts_per_page' => 10
        ]);
        
        if (empty($regular_products)) {
            echo '<p>❌ No products found.</p>';
        } else {
            echo '<p>✅ Found ' . count($regular_products) . ' products:</p>';
            echo '<ul>';
            foreach ($regular_products as $product) {
                $product_url = get_permalink($product->ID);
                echo '<li><a href="' . $product_url . '" target="_blank">' . $product->post_title . '</a> (ID: ' . $product->ID . ')</li>';
            }
            echo '</ul>';
        }
        ?>
        
        <h2>Configuration</h2>
        <table class="widefat">
            <tr>
                <td><strong>Plugin URL:</strong></td>
                <td><?php echo defined('TPB_QV_PLUGIN_URL') ? TPB_QV_PLUGIN_URL : 'Not defined'; ?></td>
            </tr>
            <tr>
                <td><strong>Plugin Path:</strong></td>
                <td><?php echo defined('TPB_QV_PLUGIN_PATH') ? TPB_QV_PLUGIN_PATH : 'Not defined'; ?></td>
            </tr>
            <tr>
                <td><strong>Debug Mode:</strong></td>
                <td><?php echo defined('WP_DEBUG') && WP_DEBUG ? '✅ Enabled' : '❌ Disabled'; ?></td>
            </tr>
        </table>
        
        <h2>🧪 How to Test</h2>
        <ol>
            <li>Go to a product page (use the links above)</li>
            <li>Look for buttons with text like "Configure", "Customize", or "Build"</li>
            <li>Click the button - it should open in a modal</li>
            <li>Check browser console (F12) for debug messages</li>
        </ol>
        
        <h2>Debug Console Messages</h2>
        <p>Look for these messages in browser console:</p>
        <ul>
            <li>🚀 TPB QuickView Modal Plugin Loading...</li>
            <li>🔍 Detecting Configure Now buttons...</li>
            <li>✅ CPB button wired for modal:</li>
            <li>🔴 Modal trigger clicked:</li>
        </ul>
    </div>
    <?php
}
?>
