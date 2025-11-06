<?php
defined( 'ABSPATH' ) || exit;
	$template                     = get_post_meta( $product_id , 'af_comp_product_layout' , true );
	$af_cp_comp_name              = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
	$af_cp_comp_desc              = (array) get_post_meta( $product_id , 'af_comp_product_component_desc' , true );
	$af_cp_comp_image             = (array) get_post_meta( $product_id , 'af_comp_product_component_image' , true );
	$af_cp_all_products           = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );
	$af_cp_categories             = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
	$af_cp_tags                   = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );
	$af_cp_default_product        = (array) get_post_meta( $product_id , 'af_composite_product_default_product' , true );
	$af_cp_required_component     = (array) get_post_meta( $product_id , 'af_composite_product_required_component' , true );
	$af_cp_sorting_cb             = (array) get_post_meta( $product_id , 'af_composite_product_sorting_cb' , true );
	$af_cp_selection_title_cb     = (array) get_post_meta( $product_id , 'af_cp_selection_title_cb' , true );
	$af_cp_selection_desc_cb      = (array) get_post_meta( $product_id , 'af_cp_selection_desc_cb' , true );
	$af_cp_selection_thumbnail_cb = (array) get_post_meta( $product_id , 'af_cp_selection_thumbnail_cb' , true );
	$af_cp_selection_price_cb     = (array) get_post_meta( $product_id , 'af_cp_selection_price_cb' , true );
	$af_cp_quantity_op            = (array) get_post_meta( $product_id , 'af_composite_product_quantity_op' , true );
	$af_cp_fixed_qty              = (array) get_post_meta( $product_id , 'af_composite_product_fixed_qty' , true );
	$af_cp_min_qty                = (array) get_post_meta( $product_id , 'af_composite_product_min_qty' , true );
	$af_cp_max_qty                = (array) get_post_meta( $product_id , 'af_composite_product_max_qty' , true );
	$af_cp_step_qty               = (array) get_post_meta( $product_id , 'af_composite_product_step_qty' , true );
	$af_cp_component_style        = (array) get_post_meta( $product_id , 'af_composite_product_component_style' , true );
	$af_cp_price_style            = (array) get_post_meta( $product_id , 'af_composite_product_price_style' , true );
	$af_cp_stock_cb               = (array) get_post_meta( $product_id , 'af_composite_product_stock_cb' , true );
	$af_cp_order                  = (array) get_post_meta( $product_id , 'af_composite_product_order' , true );
	$af_cp_type                   = (array) get_post_meta( $product_id , 'af_composite_product_type' , true );
	$af_cp_exclusive_cb           = (array) get_post_meta( $product_id , 'af_composite_product_exclusive_cb' , true );

foreach ( $af_cp_comp_name  as $comp_key => $comp_name ) {
	if ( empty( af_cp_component_products( $product_id , $comp_key )) ) {
		unset( $af_cp_comp_name[ $comp_key ] );
	}
}
	$count = 0;
if ( ( 'after_sum' == $af_cp_form_location ) && ( 'steps' == $template ) ) {
	?>
			<div class="af_cp_all_comp_tabs">
			<?php 
			$count_tabs = 0;
			foreach ( $af_cp_comp_name  as $comp_key => $comp_name ) {
				$comp_products = array();
				if ( isset($all_comp_products[ $comp_key ]) ) {
					$comp_products = $all_comp_products[ $comp_key ];
				}
				if ( empty($comp_products) ) {
					continue;
				}
				$comp_name_text = ( mb_strlen( $comp_name ) > 14 ) ? mb_substr( $comp_name, 0, 13 ) . '...' : $comp_name;
				?>
							<a data-hidden_from_scenario="no" class="af_cp_steps_btn 
							<?php 
							if ( 0 == $count_tabs ) {
								echo ' af_cp_selected_tab '; } 
							?>
							" data-val="single_comp_step_<?php echo esc_attr($product_id . '_' . $comp_key ); ?>"><?php echo esc_html__( $comp_name_text , 'af_comp_product'); ?></a>
						<?php
						++$count_tabs;
			}
			?>
				<a data-hidden_from_scenario="no" class="af_cp_steps_btn single_comp_step_review_btn" data-val="single_comp_step_review" ><?php echo esc_html__( 'Review your selection' , 'af_comp_product'); ?></a>
			</div>
		<?php
}
if ( 'vertical' == $template ) {
	?>
			<div class="af_cp_vertical_template af_cp_toggle_template">
			<?php
			foreach ( $af_cp_comp_name  as $comp_key => $comp_name ) {
				$comp_products = array();
				if ( isset($all_comp_products[ $comp_key ]) ) {
					$comp_products = $all_comp_products[ $comp_key ];
				}
				if ( empty($comp_products) ) {
					continue;
				}
				include AFCPB_DIR_PATH . '/templates/single-product/af-cp-vertical.php';
			}
			?>
				<div class="af_cp_total_price">
					<p class="price"></p>
				</div>
			</div>
		<?php
} elseif ( 'toggle' == $template ) {
	?>
		<div class="af_cp_toggle_template">
		<?php
		foreach ( $af_cp_comp_name  as $comp_key => $comp_name ) {
			$comp_products = array();
			if ( isset($all_comp_products[ $comp_key ]) ) {
				$comp_products = $all_comp_products[ $comp_key ];
			}
			if ( empty($comp_products) ) {
				continue;
			}
			include AFCPB_DIR_PATH . '/templates/single-product/af-cp-toggle.php';
		}
		?>
			<div class="af_cp_total_price">
					<p class="price"></p>
			</div>
		</div>
		<?php
} elseif ( 'steps' == $template ) {
	?>
			<div class="single_comp_step_review_div af_cp_toggle_template_hidden"></div>
			<div class="af_cp_steps_template">
			<?php
			foreach ( $af_cp_comp_name  as $comp_key => $comp_name ) {
				$comp_products = array();
				if ( isset($all_comp_products[ $comp_key ]) ) {
					$comp_products = $all_comp_products[ $comp_key ];
				}
				if ( empty($comp_products) ) {
					continue;
				}
						
				include AFCPB_DIR_PATH . '/templates/single-product/af-cp-steps.php';
			}
			?>
				<div class="af_cp_total_price">
						<p class="price"></p>
				</div>
			</div>
		<?php
}
