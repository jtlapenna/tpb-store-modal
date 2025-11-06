<?php
defined( 'ABSPATH' ) || exit;
$component_name     = (array) get_post_meta( get_the_ID() , 'af_comp_product_component_name' , true );
$component_desc     = (array) get_post_meta( get_the_ID() , 'af_comp_product_component_desc' , true );
$component_img      = (array) get_post_meta( get_the_ID() , 'af_comp_product_component_image' , true );
$component_all_prod = (array) get_post_meta( get_the_ID() , 'af_composite_product_all_products' , true );

$component_cats    = (array) get_post_meta( get_the_ID() , 'af_composite_product_categories' , true );
$component_tags    = (array) get_post_meta( get_the_ID() , 'af_composite_product_tags' , true );
$component_default = (array) get_post_meta( get_the_ID() , 'af_composite_product_default_product' , true );
$component_req     = (array) get_post_meta( get_the_ID() , 'af_composite_product_required_component' , true );

$sorting_cb          = (array) get_post_meta( get_the_ID() , 'af_composite_product_sorting_cb' , true );
$selection_title     = (array) get_post_meta( get_the_ID() , 'af_cp_selection_title_cb' , true );
$selection_desc      = (array) get_post_meta( get_the_ID() , 'af_cp_selection_desc_cb' , true );
$selection_thumbnail = (array) get_post_meta( get_the_ID() , 'af_cp_selection_thumbnail_cb' , true );

$subtotal_cart   = (array) get_post_meta( get_the_ID() , 'af_cp_subtotal_cart_cb' , true );
$subtotal_order  = (array) get_post_meta( get_the_ID() , 'af_cp_subtotal_order_cb' , true );
$selection_price = (array) get_post_meta( get_the_ID() , 'af_cp_selection_price_cb' , true );
$selection_stock = (array) get_post_meta( get_the_ID() , 'af_cp_selection_stock_cb' , true );

$component_qty_op    = (array) get_post_meta( get_the_ID() , 'af_composite_product_quantity_op' , true );
$component_fixed_qty = (array) get_post_meta( get_the_ID() , 'af_composite_product_fixed_qty' , true );
$component_min_qty   = (array) get_post_meta( get_the_ID() , 'af_composite_product_min_qty' , true );
$component_max_qty   = (array) get_post_meta( get_the_ID() , 'af_composite_product_max_qty' , true );

$component_comp_style     = (array) get_post_meta( get_the_ID() , 'af_composite_product_component_style' , true );
$component_comp_adj_type  = (array) get_post_meta( get_the_ID() , 'af_cp_comp_adj_type' , true );
$component_comp_adj_value = (array) get_post_meta( get_the_ID() , 'af_cp_comp_adj_price_value' , true );
$component_price_style    = (array) get_post_meta( get_the_ID() , 'af_composite_product_price_style' , true );

$component_stock_cb     = (array) get_post_meta( get_the_ID() , 'af_composite_product_stock_cb' , true );
$component_list_order   = (array) get_post_meta( get_the_ID() , 'af_composite_product_order' , true );
$composite_product_type = (array) get_post_meta( get_the_ID() , 'af_composite_product_type' , true );
$component_exc          = (array) get_post_meta( get_the_ID() , 'af_composite_product_exclusive_cb' , true );

?>
<div class="af_composite_product_main_div">
	<div>
		(
			<span class="btn_text_color af_cp_expand_all">
				<?php echo esc_html__( 'Expand' , 'af_comp_product' ); ?>
			</span>
			/
			<span class="btn_text_color af_cp_close_all">
				<?php echo esc_html__( 'Close' , 'af_comp_product' ); ?>
			</span>            
		)
	</div>
	<?php

	$key = 0;

	if ( !empty($component_comp_style) ) {

		foreach ($component_name as $key => $af_comp_product_component_name) {

			$af_comp_product_component_desc = '';

			if ( array_key_exists( $key , $component_desc ) ) {
				$af_comp_product_component_desc = $component_desc[ $key ];
			}

			$af_comp_product_component_image = '';

			if ( array_key_exists( $key , $component_img ) ) {
				$af_comp_product_component_image = $component_img[ $key ];
			}

			$af_composite_product_all_products = array();

			if ( array_key_exists( $key , $component_all_prod ) ) {
				$af_composite_product_all_products = (array) $component_all_prod[ $key ];
			}

			$af_composite_product_categories = array();
			
			if ( array_key_exists( $key , $component_cats ) ) {
				$af_composite_product_categories = (array) $component_cats[ $key ];
			}

			$af_composite_product_tags = array();

			if ( array_key_exists( $key , $component_tags ) ) {
				$af_composite_product_tags = (array) $component_tags[ $key ];
			}

			$af_composite_product_default_product = '';

			if ( array_key_exists( $key , $component_default ) ) {
				$af_composite_product_default_product = $component_default[ $key ];
			}

			$af_composite_product_required_component = '';

			if ( array_key_exists( $key , $component_req ) ) {
				$af_composite_product_required_component = $component_req[ $key ];
			}

			$af_composite_product_sorting_cb = '';

			if ( array_key_exists( $key , $sorting_cb ) ) {
				$af_composite_product_sorting_cb = $sorting_cb[ $key ];
			}

			$af_cp_selection_title_cb = '';

			if ( array_key_exists( $key , $selection_title ) ) {
				$af_cp_selection_title_cb = $selection_title[ $key ];
			}

			$af_cp_selection_desc_cb = '';

			if ( array_key_exists( $key , $selection_desc ) ) {
				$af_cp_selection_desc_cb = $selection_desc[ $key ];
			}

			$af_cp_selection_thumbnail_cb = '';

			if ( array_key_exists( $key , $selection_thumbnail ) ) {
				$af_cp_selection_thumbnail_cb = $selection_thumbnail[ $key ];
			}

			$af_cp_selection_price_cb = '';

			if ( array_key_exists( $key , $selection_price ) ) {
				$af_cp_selection_price_cb = $selection_price[ $key ];
			}

			$af_cp_selection_stock_cb = '';

			if ( array_key_exists( $key , $selection_stock ) ) {
				$af_cp_selection_stock_cb = $selection_stock[ $key ];
			}

			$af_composite_product_quantity_op = '';

			if ( array_key_exists( $key , $component_qty_op ) ) {
				$af_composite_product_quantity_op = $component_qty_op[ $key ];
			}

			$af_composite_product_fixed_qty = '';

			if ( array_key_exists( $key , $component_fixed_qty ) ) {
				$af_composite_product_fixed_qty = $component_fixed_qty[ $key ];
			}

			$af_composite_product_min_qty = '';

			if ( array_key_exists( $key , $component_min_qty ) ) {
				$af_composite_product_min_qty = $component_min_qty[ $key ];
			}

			$af_composite_product_max_qty = '';

			if ( array_key_exists( $key , $component_max_qty ) ) {
				$af_composite_product_max_qty = $component_max_qty[ $key ];
			}

			$af_composite_product_component_style = '';

			if ( array_key_exists( $key , $component_comp_style ) ) {
				$af_composite_product_component_style = $component_comp_style[ $key ];
			}

			$af_cp_comp_adj_type = '';

			if ( array_key_exists( $key , $component_comp_adj_type ) ) {
				$af_cp_comp_adj_type = $component_comp_adj_type[ $key ];
			}

			$af_cp_comp_adj_price_value = 0;

			if ( array_key_exists( $key , $component_comp_adj_value ) ) {
				$af_cp_comp_adj_price_value = $component_comp_adj_value[ $key ];
			}

			$af_composite_product_price_style = '';

			if ( array_key_exists( $key , $component_price_style ) ) {
				$af_composite_product_price_style = $component_price_style[ $key ];
			}

			if ( '' == $af_composite_product_price_style ) {
				continue;
			}

			$af_composite_product_stock_cb = '';

			if ( array_key_exists( $key , $component_stock_cb ) ) {
				$af_composite_product_stock_cb = $component_stock_cb[ $key ];
			}

			$af_composite_product_order = '';

			if ( array_key_exists( $key , $component_list_order ) ) {
				$af_composite_product_order = $component_list_order[ $key ];
			}

			$af_composite_product_type = '';

			if ( array_key_exists( $key , $composite_product_type ) ) {
				$af_composite_product_type = $composite_product_type[ $key ];
			}

			$af_composite_product_exclusive_cb = '';

			if ( array_key_exists( $key , $component_exc ) ) {
				$af_composite_product_exclusive_cb = $component_exc[ $key ];
			}

			include AFCPB_DIR_PATH . '/includes/product-settings/component/af-single-component.php';

		}
	}

	?>
</div>
<div class="af_composite_product_add_new_component_div">
	<button type="submit" class="button button-primary button-large"><?php echo esc_html__( 'Update components' , 'af_comp_product' ); ?></button>
	<input type="button" class="button af_composite_product_add_new_component_btn" data-size="<?php echo esc_attr($key+1); ?>" value="<?php echo esc_html__( 'Add new component' , 'af_comp_product' ); ?>">
</div>
