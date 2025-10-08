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
		// Force CPB initialization first
		console.log('🔧 Forcing CPB initialization in iframe...');
		
		// Wait for CPB scripts to load
		setTimeout(function() {
			// Check if CPB is available
			if (typeof window.af_comp_product !== 'undefined') {
				console.log('✅ CPB script loaded, initializing...');
				
				// Force CPB initialization
				if (typeof window.afcpb_init !== 'undefined') {
					window.afcpb_init();
				}
				
				// Trigger CPB hooks manually
				if (typeof jQuery !== 'undefined') {
					jQuery(document).trigger('afcpb_ready');
					jQuery(document).trigger('woocommerce_variation_has_changed');
				}
			} else {
				console.log('⚠️ CPB script not loaded, retrying...');
				// Retry after a longer delay
				setTimeout(function() {
					if (typeof window.af_comp_product !== 'undefined') {
						console.log('✅ CPB script loaded on retry');
						if (typeof window.afcpb_init !== 'undefined') {
							window.afcpb_init();
						}
						if (typeof jQuery !== 'undefined') {
							jQuery(document).trigger('afcpb_ready');
							jQuery(document).trigger('woocommerce_variation_has_changed');
						}
					}
				}, 2000);
			}
		}, 1000);
		
		// Additional CPB initialization check
		setTimeout(function() {
			console.log('🔍 Checking for CPB container after initialization...');
			const cpbContainer = document.querySelector('.afcpb-wrapper, .af_cp_all_components_content');
			if (!cpbContainer) {
				console.log('⚠️ CPB container still not found, attempting manual creation...');
				
				// Try to trigger WooCommerce product initialization
				if (typeof jQuery !== 'undefined') {
					jQuery(document.body).trigger('wc_fragment_refresh');
					jQuery(document.body).trigger('woocommerce_variation_has_changed');
					jQuery(document.body).trigger('woocommerce_update_variation_values');
				}
				
				// Check again after triggering events
				setTimeout(function() {
					const cpbContainer2 = document.querySelector('.afcpb-wrapper, .af_cp_all_components_content');
					if (cpbContainer2) {
						console.log('✅ CPB container found after manual trigger');
					} else {
						console.log('❌ CPB container still not found - CPB may not be properly configured for this product');
					}
				}, 1000);
			} else {
				console.log('✅ CPB container found:', cpbContainer);
			}
		}, 3000);
			if (!cpbContainer) {
				console.log('⚠️ CPB container still not found, attempting manual creation...');
				
				// Try to trigger WooCommerce product initialization
				if (typeof jQuery !== 'undefined') {
					jQuery(document.body).trigger('wc_fragment_refresh');
					jQuery(document.body).trigger('woocommerce_variation_has_changed');
					jQuery(document.body).trigger('woocommerce_update_variation_values');
				}
				
				// Check again after triggering events
				setTimeout(function() {
					const cpbContainer2 = document.querySelector('.afcpb-wrapper, .af_cp_all_components_content');
					if (cpbContainer2) {
						console.log('✅ CPB container found after manual trigger');
					} else {
						console.log('❌ CPB container still not found - CPB may not be properly configured for this product');
					}
				}, 1000);
			} else {
				console.log('✅ CPB container found:', cpbContainer);
			}
		}, 3000);
		
		// Create two-panel layout
		const body = document.body;
		const product = document.querySelector('.woocommerce div.product');
		
		if (product) {
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
			
			// Create a clean content container for right panel
			const contentContainer = document.createElement('div');
			contentContainer.className = 'tpb-qv-content';
			contentContainer.style.width = '100%';
			contentContainer.style.maxWidth = '100%';
			contentContainer.style.boxSizing = 'border-box';
			contentContainer.style.padding = '0';
			contentContainer.style.margin = '0';
			contentContainer.style.display = 'block';
			
			// Add only essential elements in a controlled order
			const summary = product.querySelector('.summary');
			if (summary) {
				// Add product title (h1)
				const title = summary.querySelector('h1, h2, h3, h4, h5, h6');
				if (title) {
					const titleClone = title.cloneNode(true);
					titleClone.style.marginBottom = '16px';
					titleClone.style.fontSize = '24px';
					titleClone.style.fontWeight = 'bold';
					titleClone.style.width = '100%';
					titleClone.style.maxWidth = '100%';
					titleClone.style.boxSizing = 'border-box';
					contentContainer.appendChild(titleClone);
				}
				
				// Add price (only first instance)
				const price = summary.querySelector('.price, .woocommerce-Price-amount');
				if (price) {
					const priceClone = price.cloneNode(true);
					priceClone.style.marginBottom = '16px';
					priceClone.style.fontSize = '18px';
					priceClone.style.fontWeight = 'bold';
					priceClone.style.color = '#5ac59a';
					priceClone.style.width = '100%';
					priceClone.style.maxWidth = '100%';
					priceClone.style.boxSizing = 'border-box';
					contentContainer.appendChild(priceClone);
				}
				
				// Skip stock status - not needed in modal
				
				// Aggressively remove any stock indicators that might appear
				const stockSelectors = [
					'.stock', '.woocommerce-stock', '.stock-status', '.product-stock',
					'.availability', '.woocommerce-availability', '.in-stock', '.out-of-stock',
					'.instock', '.outofstock', '.product-link', '.view-product',
					'.woocommerce-product-link', '.product-permalink'
				];
				
				stockSelectors.forEach(selector => {
					const elements = contentContainer.querySelectorAll(selector);
					elements.forEach(el => el.remove());
				});
				
				// Add "Choose SKUs" instruction (only once)
				const chooseText = summary.querySelector('p');
				if (chooseText && chooseText.textContent.includes('Choose SKUs, Mount, and Finish')) {
					const chooseTextClone = chooseText.cloneNode(true);
					chooseTextClone.style.marginBottom = '16px';
					chooseTextClone.style.fontStyle = 'italic';
					chooseTextClone.style.color = '#666';
					contentContainer.appendChild(chooseTextClone);
				}
			}
			
			// Add CPB configuration elements (variations, forms, etc.)
			const cpbElements = [
				'form.cart .variations',
				'form.cart .single_variation_wrap',
				'.woocommerce-variation',
				'.woocommerce-variation-add-to-cart',
				'.woocommerce-variation-description',
				'.woocommerce-variation-price',
				'.woocommerce-variation-availability'
			];
			
			cpbElements.forEach(selector => {
				const elements = product.querySelectorAll(selector);
				elements.forEach(element => {
					if (element) {
						const elementClone = element.cloneNode(true);
						elementClone.style.marginBottom = '16px';
						elementClone.style.width = '100%';
						elementClone.style.boxSizing = 'border-box';
						contentContainer.appendChild(elementClone);
					}
				});
			});
			
			// Add cart form (quantity and add to cart button)
			const cartForm = product.querySelector('form.cart');
			if (cartForm) {
				const cartFormClone = cartForm.cloneNode(true);
				cartFormClone.style.marginTop = '20px';
				cartFormClone.style.width = '100%';
				cartFormClone.style.boxSizing = 'border-box';
				contentContainer.appendChild(cartFormClone);
			}
			
			// Append content container to right panel
			rightPanel.appendChild(contentContainer);
			
			// Clear body and add panels
			body.innerHTML = '';
			body.appendChild(leftPanel);
			body.appendChild(rightPanel);
		}
	});
	</script>
	<?php
} );


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
