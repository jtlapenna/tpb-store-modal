<?php
/**
 * Hello Elementor Child - functions.php
 * Loads TPB Quick View modal assets and provides a helper shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue TPB Quick View assets (CSS + JS).
 * Keeps it minimal and front-end only.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() ) return;

	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();

	$css_rel = '/assets/css/tpb-qv.css';
	$js_rel  = '/assets/js/tpb-modal.js';
	$iframe_js_rel = '/assets/js/tpb-qv-iframe.js';

	$css_src = $dir . $css_rel;
	$js_src  = $dir . $js_rel;
	$iframe_js_src = $dir . $iframe_js_rel;

	$css_ver = file_exists( $path . $css_rel ) ? filemtime( $path . $css_rel ) : '1.0.0';
	$js_ver  = file_exists( $path . $js_rel )  ? filemtime( $path . $js_rel )  : '1.0.0';
	$iframe_js_ver = file_exists( $path . $iframe_js_rel ) ? filemtime( $path . $iframe_js_rel ) : '1.0.0';

	wp_enqueue_style( 'tpb-qv', $css_src, [], $css_ver );
	wp_enqueue_script( 'tpb-modal', $js_src, [], $js_ver, true );
	
	// Only load iframe JS when in quick view mode
	if ( isset( $_GET['tpb_qv'] ) ) {
		wp_enqueue_script( 'tpb-qv-iframe', $iframe_js_src, [], $iframe_js_ver, true );
	}

	// Provide runtime settings to JS (home URL and query param)
	wp_localize_script( 'tpb-modal', 'TPB_QV_CFG', [
		'home'     => home_url( '/' ),
		'qv_param' => 'tpb_qv',
	] );
	
	// Add inline script to auto-convert Configure Now buttons to modal triggers
	wp_add_inline_script( 'tpb-modal', "
		document.addEventListener('DOMContentLoaded', function() {
			// Find all 'Configure Now' buttons that link to the flower station product
			const buttons = document.querySelectorAll('a[href*=\"flower-station-configure-now\"]');
			buttons.forEach(function(btn) {
				// Add modal trigger attributes
				btn.setAttribute('data-product-url', btn.href);
				btn.classList.add('tpb-qv-trigger');
				console.log('✅ Converted Configure Now button to modal trigger:', btn.href);
			});
		});
	", 'after' );
}, 20 );


// Define deploy token constant if provided via environment (for MU-plugin cache flush)
if ( ! defined( 'TPB_DEPLOY_TOKEN' ) ) {
	$env = getenv( 'TPB_DEPLOY_TOKEN' );
	if ( $env ) {
		define( 'TPB_DEPLOY_TOKEN', $env );
	} else {
		define( 'TPB_DEPLOY_TOKEN', '1111100000102400234023024023052050204603406040120425052405603603406303' );
	}
}


// When ?tpb_qv=1 is present, add a body class and hide chrome in iframe mode
add_filter( 'body_class', function( $classes ) {
	if ( isset( $_GET['tpb_qv'] ) ) { $classes[] = 'tpb-qv'; }
	return $classes;
} );

add_action( 'wp_head', function () {
	if ( ! isset( $_GET['tpb_qv'] ) ) return;
	?>
	<style id="tpb-qv-inline">
		header, .site-header, .elementor-location-header,
		footer, .site-footer, .elementor-location-footer,
		#wpadminbar { display: none !important; }
		html, body { 
			background: #fff !important; 
			color: #333 !important;
			margin: 0 !important;
			padding: 0 !important;
			display: flex !important;
			width: 100% !important;
			height: 100vh !important;
			overflow: hidden !important;
			box-sizing: border-box !important;
		}
		/* Ensure all text is visible in iframe mode */
		body, .woocommerce, .product, .entry-content, 
		.elementor-widget, .elementor-element {
			color: #333 !important;
			background: #fff !important;
		}
		/* Improve spacing for modal content */
		.woocommerce div.product {
			padding: 0 !important;
			margin: 0 !important;
		}
		.woocommerce div.product .summary {
			padding: 0 !important;
			margin: 0 !important;
		}
		.woocommerce div.product .product_title {
			margin: 0 0 16px 0 !important;
			padding: 0 !important;
		}
		.woocommerce div.product .price {
			margin: 0 0 16px 0 !important;
			padding: 0 !important;
		}
		.woocommerce div.product .woocommerce-product-details__short-description {
			margin: 0 0 20px 0 !important;
			padding: 0 !important;
		}
		.woocommerce div.product .cart {
			margin: 0 0 20px 0 !important;
			padding: 0 !important;
		}
		.woocommerce div.product .product_meta {
			margin: 0 0 20px 0 !important;
			padding: 0 !important;
		}
		.woocommerce div.product form.cart .variations tr {
			margin: 0 0 16px 0 !important;
			padding: 0 !important;
		}
		.woocommerce div.product form.cart .variations td {
			padding: 0 0 8px 0 !important;
			margin: 0 !important;
		}
		.woocommerce div.product form.cart .variations label {
			margin: 0 0 8px 0 !important;
			padding: 0 !important;
			display: block !important;
		}
		.woocommerce div.product form.cart .variations select {
			margin: 0 0 8px 0 !important;
			padding: 8px 12px !important;
			width: 100% !important;
			max-width: 400px !important;
		}
		h1, h2, h3, h4, h5, h6 {
			color: #333 !important;
		}
		input, select, textarea, button {
			color: #333 !important;
			background: #fff !important;
			border: 1px solid #ddd !important;
		}
		a {
			color: #5ac59a !important;
		}
		.price, .woocommerce-Price-amount {
			color: #5ac59a !important;
			font-weight: bold !important;
		}
		
		/* Two-panel layout styles - ONLY in modal */
		.tpb-qv .tpb-qv-left-panel {
			width: 45% !important;
			min-width: 400px !important;
			background: #f8f9fa !important;
			border-right: 1px solid #e9ecef !important;
			display: flex !important;
			flex-direction: column !important;
			justify-content: center !important;
			align-items: center !important;
			padding: 24px !important;
			box-sizing: border-box !important;
			position: relative !important;
		}
		
		.tpb-qv .tpb-qv-right-panel {
			width: 55% !important;
			flex: 1 !important;
			background: #fff !important;
			overflow-y: auto !important;
			padding: 24px !important;
			box-sizing: border-box !important;
			position: relative !important;
		}
		
		.tpb-qv .tpb-qv-left-panel .woocommerce-product-gallery {
			width: 100% !important;
			max-width: 500px !important;
			margin: 0 !important;
			padding: 0 !important;
		}
		
		.tpb-qv .tpb-qv-left-panel .woocommerce-product-gallery img {
			width: 100% !important;
			height: auto !important;
			max-height: 600px !important;
			object-fit: contain !important;
			border-radius: 8px !important;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
		}
	</style>
	
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		console.log('🔧 CPB JavaScript Fix - Starting with improved timing...');
		
		// Wait for CPB content to be fully rendered
		function waitForCPBContent(callback, maxAttempts = 20, attempt = 1) {
			console.log('🔍 Attempt ' + attempt + ': Looking for CPB content...');
			
			// Check for CPB containers
			const cpbContainers = document.querySelectorAll('.afcpb-wrapper, .af_cp_all_components_content, .af_cp_content');
			
			if (cpbContainers.length > 0) {
				console.log('✅ CPB content found!', cpbContainers);
				callback(cpbContainers);
				return;
			}
			
			// Check for WooCommerce product content
			const productContent = document.querySelector('.woocommerce div.product, .woocommerce .product, div.product, .product');
			
			if (productContent && attempt < maxAttempts) {
				console.log('⏳ Product found but no CPB yet, waiting...');
				setTimeout(function() {
					waitForCPBContent(callback, maxAttempts, attempt + 1);
				}, 500);
			} else if (attempt >= maxAttempts) {
				console.log('❌ CPB content not found after ' + maxAttempts + ' attempts');
				// Try to create layout anyway with whatever content is available
				if (productContent) {
					console.log('🔧 Creating layout with available content...');
					callback([]);
				}
			} else {
				console.log('❌ No product content found');
			}
		}
		
		// Wait for CPB content to be ready
		waitForCPBContent(function(cpbContainers) {
			console.log('🎯 CPB content ready, creating layout...');
			
			// Try multiple selectors to find the product element
			const productSelectors = [
				'.woocommerce div.product',
				'.woocommerce .product', 
				'div.product',
				'.product',
				'.single-product .product',
				'.type-product',
				'.af_composite_product'
			];
			
			let product = null;
			for (let selector of productSelectors) {
				product = document.querySelector(selector);
				if (product) {
					console.log('✅ Found product element with selector:', selector);
					break;
				}
			}
			
			if (!product) {
				console.log('❌ No product element found with any selector');
				return;
			}
			
			console.log('✅ Product element found:', product);
			
			// Create the two-panel layout
			createTwoPanelLayout(product, cpbContainers);
		});
		
		function createTwoPanelLayout(product, cpbContainers) {
			console.log('🔧 Creating two-panel layout...');
			
			const body = document.body;
			
			// Create left panel for images
			const leftPanel = document.createElement('div');
			leftPanel.className = 'tpb-qv-left-panel';
			leftPanel.style.width = '45%';
			leftPanel.style.height = '100vh';
			leftPanel.style.flexShrink = '0';
			leftPanel.style.boxSizing = 'border-box';
			
			// Create right panel for content
			const rightPanel = document.createElement('div');
			rightPanel.className = 'tpb-qv-right-panel';
			rightPanel.style.width = '55%';
			rightPanel.style.flex = '1';
			rightPanel.style.height = '100vh';
			rightPanel.style.overflowY = 'auto';
			rightPanel.style.overflowX = 'hidden';
			rightPanel.style.boxSizing = 'border-box';
			rightPanel.style.display = 'flex';
			rightPanel.style.flexDirection = 'column';
			
			// Move product gallery to left panel
			const gallery = product.querySelector('.woocommerce-product-gallery');
			if (gallery) {
				leftPanel.appendChild(gallery.cloneNode(true));
			}
			
			// Create content container for right panel
			const contentContainer = document.createElement('div');
			contentContainer.className = 'tpb-qv-content';
			contentContainer.style.width = '100%';
			contentContainer.style.maxWidth = '100%';
			contentContainer.style.boxSizing = 'border-box';
			contentContainer.style.padding = '0';
			contentContainer.style.margin = '0';
			contentContainer.style.display = 'block';
			
			// Add product title
			const title = product.querySelector('h1, h2, h3, h4, h5, h6, .product_title');
			if (title) {
				const titleClone = title.cloneNode(true);
				titleClone.style.marginBottom = '16px';
				titleClone.style.fontSize = '24px';
				titleClone.style.fontWeight = 'bold';
				titleClone.style.width = '100%';
				contentContainer.appendChild(titleClone);
			}
			
			// Add price
			const price = product.querySelector('.price, .woocommerce-Price-amount');
			if (price) {
				const priceClone = price.cloneNode(true);
				priceClone.style.marginBottom = '16px';
				priceClone.style.fontSize = '18px';
				priceClone.style.fontWeight = 'bold';
				priceClone.style.color = '#5ac59a';
				contentContainer.appendChild(priceClone);
			}
			
			// Add CPB content - use the containers we found
			if (cpbContainers.length > 0) {
				console.log('✅ Adding CPB content to layout');
				cpbContainers.forEach(function(container) {
					const cpbClone = container.cloneNode(true);
					cpbClone.style.marginBottom = '16px';
					cpbClone.style.width = '100%';
					cpbClone.style.boxSizing = 'border-box';
					contentContainer.appendChild(cpbClone);
				});
			} else {
				console.log('⚠️ No CPB containers found, trying to find them again...');
				// Try to find CPB content again
				const cpbContainer = product.querySelector('.afcpb-wrapper, .af_cp_all_components_content, .af_cp_content');
				if (cpbContainer) {
					console.log('✅ Found CPB container on second attempt');
					const cpbClone = cpbContainer.cloneNode(true);
					cpbClone.style.marginBottom = '16px';
					cpbClone.style.width = '100%';
					cpbClone.style.boxSizing = 'border-box';
					contentContainer.appendChild(cpbClone);
				} else {
					console.log('❌ Still no CPB container found');
				}
			}
			
			// Add cart form
			const cartForm = product.querySelector('form.cart');
			if (cartForm) {
				const cartFormClone = cartForm.cloneNode(true);
				cartFormClone.style.marginTop = '20px';
				cartFormClone.style.width = '100%';
				contentContainer.appendChild(cartFormClone);
			}
			
			// Append content to right panel
			rightPanel.appendChild(contentContainer);
			
			// Clear body and add panels
			body.innerHTML = '';
			body.appendChild(leftPanel);
			body.appendChild(rightPanel);
			
			console.log('✅ Two-panel layout created successfully');
		}
	});
	</script>
	<?php
} );


// Better iframe context fix for CPB
add_action('init', 'better_fix_iframe_product_context', 1);
function better_fix_iframe_product_context() {
    if (isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') {
        // Extract product ID from URL
        $url_parts = parse_url($_SERVER['REQUEST_URI']);
        $path_parts = explode('/', trim($url_parts['path'], '/'));
        
        $product_id = null;
        foreach ($path_parts as $part) {
            if ($part === 'product') {
                $product_slug = $path_parts[array_search($part, $path_parts) + 1] ?? null;
                if ($product_slug) {
                    $product_post = get_page_by_path($product_slug, OBJECT, 'product');
                    if ($product_post) {
                        $product_id = $product_post->ID;
                        break;
                    }
                }
            }
        }
        
        if (!$product_id) {
            $product_id = 4607; // Default to our known product
        }
        
        // Set the post and product globals early
        global $post, $product;
        $post = get_post($product_id);
        $product = wc_get_product($product_id);
        
        // Force WordPress to recognize this as a product page
        add_filter('is_product', '__return_true');
        add_filter('is_single', '__return_true');
        add_filter('is_singular', '__return_true');
        add_filter('is_page', '__return_false');
        add_filter('is_home', '__return_false');
        add_filter('is_archive', '__return_false');
        add_filter('is_search', '__return_false');
        add_filter('is_feed', '__return_false');
        add_filter('is_comment_feed', '__return_false');
        add_filter('is_trackback', '__return_false');
        add_filter('is_404', '__return_false');
        add_filter('is_paged', '__return_false');
        add_filter('is_admin', '__return_false');
        add_filter('is_attachment', '__return_false');
        
        // Set up WooCommerce context
        add_action('wp', function() use ($product_id) {
            global $woocommerce_loop;
            $woocommerce_loop = array(
                'is_shortcode' => false,
                'is_paginated' => false,
                'columns' => 1,
                'name' => 'single-product'
            );
        }, 5);
    }
}

// Force CPB to render in iframe
add_action('wp', 'force_cpb_render_in_iframe', 10);
function force_cpb_render_in_iframe() {
    if (isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') {
        // Force CPB to render by hooking into the right place
        add_action('woocommerce_after_single_product_summary', function() {
            if (class_exists('ADF_Composite_Product_Front')) {
                $cpb_frontend = new ADF_Composite_Product_Front();
                $cpb_frontend->afcpb_after_composite_product_summary();
            }
        }, 10);
    }
}

// CPB Context Fix - Ensure product context is available
add_action('wp', 'setup_cpb_product_context', 6);
function setup_cpb_product_context() {
    if (is_product()) {
        global $product, $post;
        
        // Ensure product is set
        if (!$product || !is_object($product)) {
            $product = wc_get_product($post->ID);
        }
        
        // Set up WooCommerce context
        global $woocommerce_loop;
        $woocommerce_loop = array(
            'is_shortcode' => false,
            'is_paginated' => false,
            'columns' => 1,
            'name' => 'single-product'
        );
    }
}

// Force CPB Script Enqueuing Fix
add_action('wp_enqueue_scripts', 'force_cpb_scripts', 20);
function force_cpb_scripts() {
    // Only on product pages
    if (is_product()) {
        global $product;
        if ($product && $product->get_type() === 'af_composite_product') {
            // Force enqueue WooCommerce scripts
            if (function_exists('wc_enqueue_scripts')) {
                wc_enqueue_scripts();
            }
            
            // Force enqueue CPB scripts
            if (class_exists('ADF_Composite_Product_Front')) {
                $cpb_frontend = new ADF_Composite_Product_Front();
                if (method_exists($cpb_frontend, 'afcpb_front_scripts')) {
                    $cpb_frontend->afcpb_front_scripts();
                }
            }
        }
    }
}

/**
 * Convenience shortcode for adding a Quick View trigger button anywhere:
 * Usage: [tpb_qv_button product="4607" label="Configure Now"]
 */
add_shortcode( 'tpb_qv_button', function( $atts = [] ) {
	$atts = shortcode_atts( [
		'product' => '',
		'label'   => 'Configure Now',
		'class'   => '',
	], $atts, 'tpb_qv_button' );

	$product_id = absint( $atts['product'] );
	if ( ! $product_id ) return '';

	$url = get_permalink( $product_id );
	if ( ! $url ) return '';

	$classes = trim( 'tpb-qv-trigger button ' . $atts['class'] );
	$label   = esc_html( $atts['label'] );
	$url     = esc_url( $url );

	return sprintf(
		'<a class="%1$s" href="%2$s" data-product-url="%2$s">%3$s</a>',
		esc_attr( $classes ),
		$url,
		$label
	);
} );
