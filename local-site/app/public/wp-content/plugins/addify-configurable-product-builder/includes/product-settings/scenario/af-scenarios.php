<?php
defined( 'ABSPATH' ) || exit;
	$product_id         = get_the_ID();
	$scenario_enable_cb = (array) get_post_meta( $product_id , 'af_cp_enable_scenario_cb' , true );
	$scenario_name      = (array) get_post_meta( $product_id , 'af_comp_product_scenario_name' , true );
	$scenario_desc      = (array) get_post_meta( $product_id , 'af_comp_product_scenario_desc' , true );

	$hide_comp_cb                  = (array) get_post_meta( $product_id , 'af_comp_product_sc_hide_comp_cb' , true );
	$hide_options_cb               = (array) get_post_meta( $product_id , 'af_comp_product_sc_hide_options_cb' , true );
	$af_cp_sc_hide_comps_arr       = (array) get_post_meta( $product_id , 'af_cp_sc_hide_comps' , true );
	$af_cp_scenario_condition_comp = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_comp' , true );

	$af_cp_scenario_action_comp = (array) get_post_meta( $product_id , 'af_cp_scenario_action_comp' , true );
	$component_name             = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
	$action_array               = (array) get_post_meta( $product_id , 'af_cp_action_type' , true );
	$component_name             = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );

	$af_cp_sc_type_selection           = (array) get_post_meta( $product_id , 'af_cp_sc_type_selection' , true );
	$af_cp_scenario_condition_type     = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_type' , true );
	$af_cp_scenario_condition_products = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_products' , true );
	$af_composite_product_all_products = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );

	$af_composite_product_categories = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
	$af_composite_product_tags       = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );
	$component_name                  = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
	$af_cp_scenario_action_type      = (array) get_post_meta( $product_id , 'af_cp_scenario_action_type' , true );

	$af_cp_scenario_action_products    = (array) get_post_meta( $product_id , 'af_cp_scenario_action_products' , true );
	$af_composite_product_all_products = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );
	$af_composite_product_categories   = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
	$af_composite_product_tags         = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );
	$key                               = 0;
?>
	<div class="af_cp_expand_close">
		(
			<span class="btn_text_color af_cp_expand_all_sce">
				<?php echo esc_html__( 'Expand' , 'af_comp_product' ); ?>
			</span>
			/
			<span class="btn_text_color af_cp_close_all_sce">
				<?php echo esc_html__( 'Close' , 'af_comp_product' ); ?>
			</span>            
		)
	</div>
	<div class="af_cp_scenario_main_div">
		<?php

		if ( !empty($scenario_name) ) {

			foreach ($scenario_name as $key => $af_comp_product_scenario_name) {
				if ( !isset($scenario_name[ $key ]) ) {
					continue;
				}
				if ( empty($af_comp_product_scenario_name) ) {
					continue;
				}
				$af_comp_product_scenario_desc = '';

				if ( isset($scenario_desc[ $key ]) ) {
					$af_comp_product_scenario_desc = $scenario_desc[ $key ];
				}

				$af_cp_enable_scenario_cb = '';

				if ( isset($scenario_enable_cb[ $key ]) ) {
					$af_cp_enable_scenario_cb = $scenario_enable_cb[ $key ];
				}

				$af_comp_product_sc_hide_comp_cb = '';

				if ( isset($hide_comp_cb[ $key ]) ) {
					$af_comp_product_sc_hide_comp_cb = $hide_comp_cb[ $key ];
				}

				$af_comp_product_sc_hide_options_cb = '';

				if ( isset($hide_options_cb[ $key ]) ) {
					$af_comp_product_sc_hide_options_cb = $hide_options_cb[ $key ];
				}

				$af_cp_sc_hide_comps = array();

				if ( isset($af_cp_sc_hide_comps_arr[ $key ]) ) {
					$af_cp_sc_hide_comps = (array) $af_cp_sc_hide_comps_arr[ $key ];
				}

				include AFCPB_DIR_PATH . '/includes/product-settings/scenario/af-single-scenario.php';
			}
		}
		?>
	</div>
	<div class="af_cp_update_scenario_div">
		<button type="submit" class="button button-primary button-large"><?php echo esc_html__( 'Update scenarios' , 'af_comp_product' ); ?></button>
		<input type="button" class="button af_cp_add_new_scenario_btn" data-product_id="<?php echo esc_attr($product_id); ?>" data-size="<?php echo esc_attr($key+1); ?>" value="<?php echo esc_html__( 'Add new scenario' , 'af_comp_product' ); ?>">
	</div>

