<?php
/**
 * Test Product Page - Minimal WordPress setup for testing modal
 */

// Basic WordPress setup
define('WP_USE_THEMES', false);
require_once('./wp-load.php');

// Set up basic product data
$product_id = 123;
$product_name = 'Test Flower Station';
$product_price = '$1,299.00';
$product_image = 'https://via.placeholder.com/400x300?text=Flower+Station';

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product_name; ?> - TPB Test</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- WordPress head -->
    <?php wp_head(); ?>
    
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .product-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-header {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .product-image {
            flex: 1;
            max-width: 400px;
        }
        .product-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .product-info {
            flex: 1;
        }
        .product-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .product-price {
            font-size: 24px;
            font-weight: bold;
            color: #5ac59a;
            margin-bottom: 20px;
        }
        .product-description {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
            margin-bottom: 20px;
        }
        .configure-button {
            background: linear-gradient(135deg, #5ac59a 0%, #4a9d7a 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .configure-button:hover {
            background: linear-gradient(135deg, #4a9d7a 0%, #3d8b6a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(90, 197, 154, 0.3);
        }
        .test-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <div class="product-container">
        <div class="test-info">
            <strong>🧪 Test Mode:</strong> This is a test product page for the TPB QuickView Modal system.
            <br><strong>URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?>
        </div>
        
        <div class="product-header">
            <div class="product-image">
                <img src="<?php echo $product_image; ?>" alt="<?php echo $product_name; ?>">
            </div>
            <div class="product-info">
                <h1 class="product-title"><?php echo $product_name; ?></h1>
                <div class="product-price"><?php echo $product_price; ?></div>
                <div class="product-description">
                    This is a test product for the TPB QuickView Modal system. The modal should open when you click the "Configure Now" button below, showing the native stepper interface.
                </div>
                
                <!-- CPB Button that should trigger the modal -->
                <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" 
                   class="afc_add_to_cart_button configure-button" 
                   data-tpb-quickview="1"
                   data-product-url="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    Configure Now
                </a>
            </div>
        </div>
        
        <div class="woocommerce">
            <div class="product">
                <div class="woocommerce-product-gallery">
                    <div class="woocommerce-product-gallery__image">
                        <img src="<?php echo $product_image; ?>" alt="<?php echo $product_name; ?>">
                    </div>
                </div>
                
                <div class="summary">
                    <h1 class="product_title"><?php echo $product_name; ?></h1>
                    <div class="price"><?php echo $product_price; ?></div>
                    
                    <form class="cart">
                        <div class="quantity">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1">
                        </div>
                        
                        <button type="submit" class="single_add_to_cart_button button">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- WordPress footer -->
    <?php wp_footer(); ?>
</body>
</html>
