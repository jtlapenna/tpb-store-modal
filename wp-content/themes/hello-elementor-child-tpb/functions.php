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
