<?php
/**
 * Hello Elementor Child - functions.php
 * Loads TPB Quick View modal assets and provides a helper shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REMOVED: Modal assets now handled by tpb-quickview-modal plugin
 * See /wp-content/plugins/tpb-quickview-modal/tpb-quickview-modal.php
 * 
 * The following code was removed because:
 * - Child theme modal files are not being loaded
 * - Plugin handles all modal functionality
 * - Prevents conflicts and confusion
 */


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
	// Hide content immediately to prevent visible reconfiguration
	document.body.style.opacity = '0';
	
	document.addEventListener('DOMContentLoaded', function() {
		// Single CPB initialization with smart polling - NO MORE RECONFIGURATION!
		console.log('🔧 Initializing CPB in iframe (single attempt)...');
		
		let attempts = 0;
		const maxAttempts = 6; // Wait longer for CPB to load dynamically
		let layoutCreated = false;
		let cpbAttached = false;
		
		function initializeCPB() {
			attempts++;
			console.log(`🔍 CPB initialization attempt ${attempts}/${maxAttempts}`);
			
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
			}
			
			// Check for CPB container
			const cpbContainer = document.querySelector('.afcpb-wrapper, .af_cp_all_components_content');
			if (cpbContainer) {
				console.log('✅ CPB container found:', cpbContainer);
				// Ensure layout exists, then attach CPB (move, not clone)
				if (!layoutCreated) {
					createTwoPanelLayout();
					layoutCreated = true;
				}
				attachCPBIntoLayoutIfAvailable();
				return true;
			}
			
			// Retry if not found and under max attempts - BUT ONLY ONCE
			if (attempts < maxAttempts) {
				setTimeout(initializeCPB, 300); // Reduced delay
			} else {
				console.log('❌ CPB initialization timed out; layout stays and observer will attach when ready');
			}
			
			return false;
		}
		
		// Start single initialization
		initializeCPB();
	});
	
	function createTwoPanelLayout() {
		console.log('🔧 Creating two-panel layout...');
		
		const body = document.body;
		
		// Find product element
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
			console.log('❌ No product element found, using body');
			product = body;
		}
		
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
		
		// Try to attach CPB now (may not be ready yet)
		attachCPBIntoLayoutIfAvailable();
		
		// Add cart form (MOVE to keep bindings intact)
		const cartForm = product.querySelector('form.cart');
		if (cartForm && !document.body.contains(cartForm.parentElement?.closest('.tpb-qv-content'))) {
			cartForm.style.marginTop = '20px';
			cartForm.style.width = '100%';
			contentContainer.appendChild(cartForm); // MOVE, don't clone
		}
		
		// Append content to right panel
		rightPanel.appendChild(contentContainer);
		
		// Clear body and add panels
		body.innerHTML = '';
		body.appendChild(leftPanel);
		body.appendChild(rightPanel);
		
		// Show content smoothly
		body.style.opacity = '1';
		body.style.transition = 'opacity 0.3s ease';
		
		console.log('✅ Two-panel layout created successfully');
		
		// Observe for CPB container arriving later
		const observer = new MutationObserver(() => {
			if (!cpbAttached) attachCPBIntoLayoutIfAvailable();
		});
		observer.observe(document.body, { childList: true, subtree: true });
		// Strong visibility guard for CPB once layout exists
		ensureCPBVisibleStrong();
	}

	function attachCPBIntoLayoutIfAvailable() {
		const product = document.querySelector('.woocommerce div.product, .woocommerce .product, div.product, .product, .single-product .product, .type-product, .af_composite_product');
		const contentContainer = document.querySelector('.tpb-qv-content');
		if (!product || !contentContainer) return;
		const cpbContainer = product.querySelector('.afcpb-wrapper, .af_cp_all_components_content, .af_cp_content');
		if (!cpbContainer) return;
		// Move and normalize visibility
		cpbContainer.style.marginBottom = '16px';
		cpbContainer.style.width = '100%';
		cpbContainer.style.boxSizing = 'border-box';
		cpbContainer.style.display = 'block';
		const firstStep = cpbContainer.querySelector('.single_component');
		if (firstStep) {
			firstStep.style.display = 'block';
			firstStep.style.visibility = 'visible';
			firstStep.classList.add('tpb-visible');
			firstStep.classList.remove('tpb-hidden');
		}
		if (!contentContainer.contains(cpbContainer)) {
			contentContainer.appendChild(cpbContainer);
			cpbAttached = true;
			console.log('✅ CPB attached into modal layout');
		}
	}

	function ensureCPBVisibleStrong() {
		// Inject CSS safeguards
		const styleId = 'tpb-cpb-visibility-guard';
		if (!document.getElementById(styleId)) {
			const s = document.createElement('style');
			s.id = styleId;
			s.textContent = `
				.af_cp_all_components_content, .af_cp_vertical_template, .af_cp_toggle_template { display: block !important; visibility: visible !important; }
				.single_component.tpb-visible, .single_component.tpb-force-visible-first { display: block !important; visibility: visible !important; }
			`;
			document.head.appendChild(s);
		}
		const cpbContainer = document.querySelector('.af_cp_all_components_content, .afcpb-wrapper, .af_cp_content');
		if (!cpbContainer) return;
		cpbContainer.style.display = 'block';
		const first = cpbContainer.querySelector('.single_component');
		if (first) {
			first.classList.add('tpb-force-visible-first');
			first.style.display = 'block';
			first.style.visibility = 'visible';
		}
		// Re-enforce if anything flips back to hidden
		const mo = new MutationObserver((muts) => {
			if (!first) return;
			const cs = window.getComputedStyle(first);
			if (cs.display === 'none') {
				first.style.display = 'block';
			}
		});
		mo.observe(cpbContainer, { attributes: true, subtree: true, attributeFilter: ['style', 'class'] });
		// Run a few retries in early load
		let tries = 0; const id = setInterval(() => { tries++; attachCPBIntoLayoutIfAvailable(); if (tries > 10) clearInterval(id); }, 200);
	}
	</script>
	<?php
} );


// CPB Context Fix - Ensure product context is available
add_action('wp', 'setup_cpb_product_context', 5);
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

// TPB QuickView API: register REST route to fetch products by tags/categories (theme-level to ensure availability)
add_action('rest_api_init', function(){
    register_rest_route('tpb/v1','/qv/products', [
        'methods'  => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function( WP_REST_Request $req ){
            $tags = array_values(array_filter((array)$req->get_param('tags')));
            $cats = array_values(array_filter((array)$req->get_param('categories')));
            $limit = max(1, min(48, intval($req->get_param('limit') ?: 24)));
            $sort = $req->get_param('sort') ?: 'price_asc';

            $tax = ['relation'=>'AND'];
            if(!empty($tags)){
                $tax[] = [ 'taxonomy'=>'product_tag','field'=>'slug','terms'=>array_map('sanitize_title',$tags),'operator'=>'AND' ];
            }
            if(!empty($cats)){
                $tax[] = [ 'taxonomy'=>'product_cat','field'=>'slug','terms'=>array_map('sanitize_title',$cats),'operator'=>'AND' ];
            }

            $args = [ 'post_type'=>'product','post_status'=>'publish','posts_per_page'=>$limit,'tax_query'=>$tax ];
            if($sort==='price_asc'){ $args['meta_key']='_price'; $args['orderby']='meta_value_num'; $args['order']='ASC'; }

            $q = new WP_Query($args); $items=[];
            if($q->have_posts()){
                foreach($q->posts as $p){ $product = wc_get_product($p); if(!$product) continue; $img_id=$product->get_image_id(); $img=$img_id? wp_get_attachment_image_url($img_id,'large') : wc_placeholder_img_src('large');
                    $items[] = [ 'id'=>$product->get_id(), 'name'=>html_entity_decode($product->get_name()), 'price_html'=>$product->get_price_html(), 'image_url'=>$img, 'permalink'=>get_permalink($product->get_id()), 'is_purchasable'=>$product->is_purchasable(), 'supports_qty'=>!$product->is_sold_individually() ];
                }
            }
            wp_reset_postdata();
            return rest_ensure_response([ 'results'=>$items ]);
        }
    ]);
});

// AJAX fallback for native configurator (works if REST is blocked)
add_action('wp_ajax_nopriv_tpb_qv_products', 'tpb_qv_products_ajax_theme');
add_action('wp_ajax_tpb_qv_products', 'tpb_qv_products_ajax_theme');
function tpb_qv_products_ajax_theme(){
	$tags = isset($_REQUEST['tags']) ? array_filter(array_map('sanitize_title', explode(',', (string)$_REQUEST['tags']))) : [];
	$cats = isset($_REQUEST['categories']) ? array_filter(array_map('sanitize_title', explode(',', (string)$_REQUEST['categories']))) : [];
	$limit = isset($_REQUEST['limit']) ? max(1, min(48, intval($_REQUEST['limit']))) : 24;

	$tax = [ 'relation' => 'AND' ];
	if (!empty($tags)){
		$tax[] = [ 'taxonomy'=>'product_tag', 'field'=>'slug', 'terms'=>$tags, 'operator'=>'AND' ];
	}
	if (!empty($cats)){
		$tax[] = [ 'taxonomy'=>'product_cat', 'field'=>'slug', 'terms'=>$cats, 'operator'=>'AND' ];
	}

	$args = [
		'post_type' => 'product',
		'post_status' => 'publish',
		'posts_per_page' => $limit,
		'tax_query' => $tax,
		'meta_key' => '_price',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
	];
	$q = new WP_Query($args);
	$items = [];
	if ($q->have_posts()){
		foreach($q->posts as $p){
			$product = wc_get_product($p);
			if(!$product) continue;
			$img_id = $product->get_image_id();
			$img = $img_id ? wp_get_attachment_image_url($img_id,'large') : wc_placeholder_img_src('large');
			$items[] = [
				'id' => $product->get_id(),
				'name' => html_entity_decode($product->get_name()),
				'price_html' => $product->get_price_html(),
				'image_url' => $img,
				'permalink' => get_permalink($product->get_id()),
				'is_purchasable' => $product->is_purchasable(),
				'supports_qty' => !$product->is_sold_individually(),
			];
		}
	}
	wp_reset_postdata();
	wp_send_json([ 'results' => $items ]);
}
