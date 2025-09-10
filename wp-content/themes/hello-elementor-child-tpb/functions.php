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
			padding: 24px !important;
			margin: 0 !important;
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
	</style>
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
