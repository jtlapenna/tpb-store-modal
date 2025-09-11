<?php
/**
 * CPB Component Setup Script
 * Run this in WordPress admin or via WP-CLI to set up CPB components
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create dummy products for CPB strategy choices
 */
function tpb_create_cpb_dummy_products() {
    $dummy_products = [
        [
            'name' => 'Custom Build Strategy',
            'sku' => 'CUSTOM-BUILD-STRATEGY',
            'price' => 0,
            'description' => 'Dummy product for Custom Build strategy selection'
        ],
        [
            'name' => 'Pre-designed Strategy', 
            'sku' => 'PREDESIGNED-STRATEGY',
            'price' => 0,
            'description' => 'Dummy product for Pre-designed strategy selection'
        ],
        [
            'name' => 'Counter Mount',
            'sku' => 'COUNTER-MOUNT',
            'price' => 0,
            'description' => 'Dummy product for Counter Mount selection'
        ],
        [
            'name' => 'Wall Mount',
            'sku' => 'WALL-MOUNT', 
            'price' => 0,
            'description' => 'Dummy product for Wall Mount selection'
        ],
        [
            'name' => 'Free-standing',
            'sku' => 'FREESTANDING',
            'price' => 0,
            'description' => 'Dummy product for Free-standing selection'
        ]
    ];

    foreach ($dummy_products as $product_data) {
        // Check if product already exists
        $existing = wc_get_product_id_by_sku($product_data['sku']);
        if ($existing) {
            echo "Product {$product_data['name']} already exists (ID: {$existing})\n";
            continue;
        }

        // Create simple product
        $product = new WC_Product_Simple();
        $product->set_name($product_data['name']);
        $product->set_sku($product_data['sku']);
        $product->set_price($product_data['price']);
        $product->set_regular_price($product_data['price']);
        $product->set_description($product_data['description']);
        $product->set_status('publish');
        $product->set_catalog_visibility('hidden'); // Hide from catalog
        $product->set_featured(false);
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');

        $product_id = $product->save();
        
        if ($product_id) {
            echo "Created product: {$product_data['name']} (ID: {$product_id})\n";
        } else {
            echo "Failed to create product: {$product_data['name']}\n";
        }
    }
}

/**
 * Get hardware kit SKUs for Component 1
 */
function tpb_get_hardware_kit_skus() {
    // Replace these with your actual hardware kit SKUs
    $hardware_kits = [
        '8-SKU-HARDWARE-KIT',
        '12-SKU-HARDWARE-KIT', 
        '16-SKU-HARDWARE-KIT'
    ];

    $products = [];
    foreach ($hardware_kits as $sku) {
        $product_id = wc_get_product_id_by_sku($sku);
        if ($product_id) {
            $product = wc_get_product($product_id);
            $products[] = [
                'id' => $product_id,
                'name' => $product->get_name(),
                'sku' => $sku,
                'price' => $product->get_price()
            ];
        }
    }

    return $products;
}

/**
 * Get complete bundle SKUs for Component 4
 */
function tpb_get_complete_bundle_skus() {
    // Replace these with your actual complete bundle SKUs
    $bundle_skus = [
        'REMEDY-16-SKU-COUNTER-WALNUT-COMPLETE',
        'REMEDY-16-SKU-WALL-WALNUT-COMPLETE',
        'REMEDY-16-SKU-FREESTANDING-WALNUT-COMPLETE',
        'GREEN-CLOUD-12-SKU-COUNTER-ACRYLIC-COMPLETE',
        'GREEN-CLOUD-12-SKU-WALL-ACRYLIC-COMPLETE',
        'GREEN-CLOUD-12-SKU-FREESTANDING-ACRYLIC-COMPLETE'
    ];

    $products = [];
    foreach ($bundle_skus as $sku) {
        $product_id = wc_get_product_id_by_sku($sku);
        if ($product_id) {
            $product = wc_get_product($product_id);
            $products[] = [
                'id' => $product_id,
                'name' => $product->get_name(),
                'sku' => $sku,
                'price' => $product->get_price()
            ];
        }
    }

    return $products;
}

/**
 * Display CPB setup instructions
 */
function tpb_display_cpb_setup_instructions() {
    echo "\n=== CPB COMPONENT SETUP INSTRUCTIONS ===\n\n";
    
    echo "1. HARDWARE KIT SKUs (Component 1):\n";
    $hardware_kits = tpb_get_hardware_kit_skus();
    foreach ($hardware_kits as $kit) {
        echo "   - {$kit['name']} (SKU: {$kit['sku']}) - \${$kit['price']}\n";
    }
    
    echo "\n2. DUMMY PRODUCTS (Components 2 & 3):\n";
    echo "   - Custom Build Strategy (SKU: CUSTOM-BUILD-STRATEGY)\n";
    echo "   - Pre-designed Strategy (SKU: PREDESIGNED-STRATEGY)\n";
    echo "   - Counter Mount (SKU: COUNTER-MOUNT)\n";
    echo "   - Wall Mount (SKU: WALL-MOUNT)\n";
    echo "   - Free-standing (SKU: FREESTANDING)\n";
    
    echo "\n3. COMPLETE BUNDLE SKUs (Component 4):\n";
    $bundles = tpb_get_complete_bundle_skus();
    foreach ($bundles as $bundle) {
        echo "   - {$bundle['name']} (SKU: {$bundle['sku']}) - \${$bundle['price']}\n";
    }
    
    echo "\n4. NEXT STEPS:\n";
    echo "   a) Run: tpb_create_cpb_dummy_products() to create dummy products\n";
    echo "   b) Go to Addify CPB and set up components:\n";
    echo "      - Component 1: Link to hardware kit SKUs\n";
    echo "      - Component 2: Link to strategy dummy products\n";
    echo "      - Component 3: Link to mount dummy products\n";
    echo "      - Component 4: Link to complete bundle SKUs\n";
    echo "   c) Configure conditional logic between components\n";
    echo "   d) Test the complete flow\n\n";
}

// Run setup if called directly
if (isset($_GET['tpb_setup_cpb'])) {
    tpb_create_cpb_dummy_products();
    tpb_display_cpb_setup_instructions();
}
