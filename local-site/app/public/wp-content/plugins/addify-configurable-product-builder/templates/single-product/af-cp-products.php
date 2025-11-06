<?php
defined( 'ABSPATH' ) || exit;
	$default_product   = af_cp_component_default_product( $product_id , $comp_key );
	$af_cp_quantity_op = (array) get_post_meta( $product_id , 'af_composite_product_quantity_op' , true );
	$qty_option        = '';
if ( isset($af_cp_quantity_op[ $comp_key ]) ) {
	$qty_option = $af_cp_quantity_op[ $comp_key ];
}
	$fixed_qty_text = '';
	$fixed_qty      = 1;
	
if ( 'fixed' == $qty_option ) {
	$fixed_qty_op = (array) get_post_meta( $product_id , 'af_composite_product_fixed_qty' , true );
	if ( isset($fixed_qty_op[ $comp_key ]) ) {
		$fixed_qty = $fixed_qty_op[ $comp_key ];
	}
	if ( '' == $fixed_qty ) {
		$fixed_qty = 1;
	}
	$fixed_qty_text = $fixed_qty . ' &times; ';
} else {
	
	$min = get_min_qty_component( $product_id , $comp_key );
	$max = get_max_qty_component( $product_id , $comp_key );
	if ( '' != $max ) {
		if ( ( $min == $max ) || ( $min > $max ) ) {
			$qty_option     = 'fixed';
			$fixed_qty_text = $min . ' &times; ';
		}
	}
}




if ( 'simple' == $list_style ) {
	af_cp_simple_dropdown( $product_id , $comp_key , $default_product , $comp_products , $fixed_qty_text , $fixed_qty , $qty_option );
} elseif ( 'image_product' == $list_style ) {
	af_cp_image_dropdown( $product_id , $comp_key , $default_product , $comp_products , $fixed_qty_text , $fixed_qty , $qty_option );
} elseif ( 'radio' == $list_style ) {
	af_cp_radio_options( $product_id , $comp_key , $default_product , $comp_products , $fixed_qty_text , $fixed_qty , $qty_option );
} elseif ( 'thumbnail' == $list_style ) {
	af_cp_thumbnail_options( $product_id , $comp_key , $default_product , $comp_products , $fixed_qty_text , $fixed_qty , $qty_option , 1 );
}
	//af_cp_component_common_qty( $product_id , $qty_option , $comp_key , $fixed_qty );
