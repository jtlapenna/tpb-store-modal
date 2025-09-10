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

	$css_src = $dir . $css_rel;
	$js_src  = $dir . $js_rel;

	$css_ver = file_exists( $path . $css_rel ) ? filemtime( $path . $css_rel ) : '1.0.0';
	$js_ver  = file_exists( $path . $js_rel )  ? filemtime( $path . $js_rel )  : '1.0.0';

	wp_enqueue_style( 'tpb-qv', $css_src, [], $css_ver );
	wp_enqueue_script( 'tpb-modal', $js_src, [], $js_ver, true );

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
			height: 100vh !important;
			overflow: hidden !important;
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
		
		/* Two-panel layout styles */
		.tpb-qv-left-panel {
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
		
		.tpb-qv-right-panel {
			width: 55% !important;
			background: #fff !important;
			overflow-y: auto !important;
			padding: 24px !important;
			box-sizing: border-box !important;
			position: relative !important;
		}
		
		.tpb-qv-left-panel .woocommerce-product-gallery {
			width: 100% !important;
			max-width: 500px !important;
			margin: 0 !important;
			padding: 0 !important;
		}
		
		.tpb-qv-left-panel .woocommerce-product-gallery img {
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
				rightPanel.style.height = '100vh';
				rightPanel.style.overflowY = 'auto';
				rightPanel.style.overflowX = 'hidden';
				rightPanel.style.boxSizing = 'border-box';
			
			// Move product gallery to left panel
			const gallery = product.querySelector('.woocommerce-product-gallery');
			if (gallery) {
				leftPanel.appendChild(gallery.cloneNode(true));
			}
			
			// Move summary content to right panel (with noise filtering)
			const summary = product.querySelector('.summary');
			if (summary) {
				// Clone summary and clean it up
				const cleanSummary = summary.cloneNode(true);
				
				// Remove redundant elements
				const elementsToRemove = [
					'.woocommerce-product-rating',
					'.woocommerce-product-details__short-description',
					'.product_meta',
					'.woocommerce-tabs',
					'.woocommerce-product-attributes',
					'.related.products',
					'.upsells.products',
					'.cross-sells.products'
				];
				
				elementsToRemove.forEach(selector => {
					const elements = cleanSummary.querySelectorAll(selector);
					elements.forEach(el => el.remove());
				});
				
				// Remove duplicate "Add to quote" buttons (keep only the first one)
				const addToQuoteButtons = cleanSummary.querySelectorAll('a[href*="add-to-quote"], button');
				addToQuoteButtons.forEach((btn, index) => {
					if (btn.textContent.includes('Add to quote') && index > 0) {
						btn.remove();
					}
				});
				
				// Remove duplicate pricing
				const prices = cleanSummary.querySelectorAll('.price, .woocommerce-Price-amount');
				prices.forEach((price, index) => {
					if (index > 0) price.remove();
				});
				
				// Remove any duplicate product blocks or sections
				const productBlocks = cleanSummary.querySelectorAll('.woocommerce div.product');
				productBlocks.forEach((block, index) => {
					if (index > 0) block.remove();
				});
				
				// Remove duplicate "Choose SKUs" text
				const chooseTexts = cleanSummary.querySelectorAll('p');
				chooseTexts.forEach((p, index) => {
					if (p.textContent.includes('Choose SKUs, Mount, and Finish') && index > 0) {
						p.remove();
					}
				});
				
				rightPanel.appendChild(cleanSummary);
			}
			
			// Add essential CPB configuration elements to right panel
			const essentialSelectors = [
				'.woocommerce-variation',
				'.woocommerce-variation-add-to-cart',
				'.woocommerce-variation-description',
				'.woocommerce-variation-price',
				'.woocommerce-variation-availability',
				'form.cart .variations',
				'form.cart .single_variation_wrap',
				'form.cart',
				'.woocommerce div.product form',
				'.variations',
				'.single_variation_wrap',
				'.woocommerce-variation-add-to-cart',
				'.woocommerce-variation-description',
				'.woocommerce-variation-price',
				'.woocommerce-variation-availability'
			];
			
			essentialSelectors.forEach(selector => {
				const elements = product.querySelectorAll(selector);
				elements.forEach(element => {
					if (element && !rightPanel.contains(element)) {
						rightPanel.appendChild(element.cloneNode(true));
					}
				});
			});
			
			// Add only essential CPB configuration elements (avoiding duplicates)
			const cpbSelectors = [
				'form.cart .variations',
				'form.cart .single_variation_wrap',
				'.woocommerce-variation',
				'.woocommerce-variation-add-to-cart',
				'.woocommerce-variation-description',
				'.woocommerce-variation-price',
				'.woocommerce-variation-availability'
			];
			
			cpbSelectors.forEach(selector => {
				const elements = product.querySelectorAll(selector);
				elements.forEach(element => {
					if (element && !rightPanel.contains(element)) {
						rightPanel.appendChild(element.cloneNode(true));
					}
				});
			});
			
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
