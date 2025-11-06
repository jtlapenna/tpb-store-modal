<?php
defined( 'ABSPATH' ) || exit;

if ( empty( $_POST['af_cp_shipping_fields_nonce'] ) || !wp_verify_nonce(sanitize_text_field($_POST['af_cp_shipping_fields_nonce']), 'af_cp_shipping_fields_nonce')) {

	wp_die( esc_html__('Security Violated Error!', 'af_comp_product') );
}


if ( isset( $_POST['af_comp_product_price_type'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_price_type', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_price_type'] ) , '' ) );
}

if ( isset( $_POST['af_cp_form_location'] ) ) {
	update_post_meta( $post_id, 'af_cp_form_location', sanitize_meta( '' , wp_unslash( $_POST['af_cp_form_location'] ) , '' ) );
}

if ( isset( $_POST['af_comp_product_price_adjustment'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_price_adjustment', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_price_adjustment'] ) , '' ) );
}

if ( isset( $_POST['af_comp_product_adj_value'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_adj_value', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_adj_value'] ) , '' ) );
}

if ( isset( $_POST['af_comp_product_adj_min_qty'] ) ) {

	if ( '' != $_POST['af_comp_product_adj_min_qty'] ) {
		update_post_meta( $post_id, 'af_comp_product_adj_min_qty', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_adj_min_qty'] ) , '' ) );
	} else {
		update_post_meta( $post_id, 'af_comp_product_adj_min_qty', '1' );
	}
} else {
	update_post_meta( $post_id, 'af_comp_product_adj_min_qty', '1' );
}

if ( isset( $_POST['af_comp_product_adj_max_qty'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_adj_max_qty', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_adj_max_qty'] ) , '' ) );
}

if ( isset( $_POST['af_comp_product_adj_step_qty'] ) ) {

	if ( '' != $_POST['af_comp_product_adj_step_qty'] ) {
		update_post_meta( $post_id, 'af_comp_product_adj_step_qty', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_adj_step_qty'] ) , '' ) );
	} else {
		update_post_meta( $post_id, 'af_comp_product_adj_step_qty', 1 );
	}
} else {
	update_post_meta( $post_id, 'af_comp_product_adj_step_qty', 1 );
}

if ( isset( $_POST['af_comp_product_text'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_text', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_text'] ) , '' ) );
}

if ( isset( $_POST['af_cp_allow_edit_in_cart'] ) ) {
	update_post_meta( $post_id, 'af_cp_allow_edit_in_cart', sanitize_meta( '' , wp_unslash( $_POST['af_cp_allow_edit_in_cart'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_allow_edit_in_cart', 'no' );
}

if ( isset( $_POST['af_comp_product_component_name'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_component_name', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_component_name'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_component_name', array() );
}

if ( isset( $_POST['af_comp_product_component_image'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_component_image', sanitize_meta( '' , wp_unslash( (array) $_POST['af_comp_product_component_image'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_component_image', array() );
}

if ( isset( $_POST['af_comp_product_component_desc'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_component_desc', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_component_desc'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_component_desc', array() );
}

if ( isset( $_POST['af_composite_product_all_products'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_all_products', sanitize_meta( '' , wp_unslash( (array) $_POST['af_composite_product_all_products'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_all_products', array() );
}

if ( isset( $_POST['af_composite_product_categories'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_categories', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_categories'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_categories', array() );
}

if ( isset( $_POST['af_composite_product_tags'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_tags', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_tags'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_tags', array() );
}

if ( isset( $_POST['af_composite_product_default_product'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_default_product', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_default_product'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_default_product', array() );
}

if ( isset( $_POST['af_composite_product_required_component'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_required_component', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_required_component'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_required_component', array() );
}

if ( isset( $_POST['af_composite_product_quantity_op'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_quantity_op', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_quantity_op'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_quantity_op', array() );
}

if ( isset( $_POST['af_composite_product_fixed_qty'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_fixed_qty', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_fixed_qty'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_fixed_qty', array() );
}

if ( isset( $_POST['af_composite_product_min_qty'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_min_qty', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_min_qty'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_min_qty', array() );
}

if ( isset( $_POST['af_composite_product_max_qty'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_max_qty', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_max_qty'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_max_qty', array() );
}

if ( isset( $_POST['af_composite_product_component_style'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_component_style', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_component_style'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_component_style', array() );
}

if ( isset( $_POST['af_composite_product_price_style'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_price_style', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_price_style'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_price_style', array() );
}

if ( isset( $_POST['af_composite_product_stock_cb'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_stock_cb', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_stock_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_stock_cb', array() );
}

if ( isset( $_POST['af_composite_product_order'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_order', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_order'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_order', array() );
}

if ( isset( $_POST['af_composite_product_type'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_type', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_type'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_type', array() );
}

if ( isset( $_POST['af_composite_product_exclusive_cb'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_exclusive_cb', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_exclusive_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_exclusive_cb', array() );
}

if ( isset( $_POST['af_comp_product_scenario_name'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_scenario_name', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_scenario_name'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_scenario_name', array() );
}

if ( isset( $_POST['af_comp_product_scenario_desc'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_scenario_desc', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_scenario_desc'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_scenario_desc', array() );
}

if ( isset( $_POST['af_comp_product_sc_hide_comp_cb'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_sc_hide_comp_cb', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_sc_hide_comp_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_sc_hide_comp_cb', array() );
}

if ( isset( $_POST['af_comp_product_sc_hide_options_cb'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_sc_hide_options_cb', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_sc_hide_options_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_sc_hide_options_cb', array() );
}

if ( isset( $_POST['af_cp_scenario_condition_comp'] ) ) {
	$af_cp_scenario_condition_comp = sanitize_meta( '' , wp_unslash( $_POST['af_cp_scenario_condition_comp'] ) , '' );

	foreach ( $af_cp_scenario_condition_comp as $scenario_key => $scenario_value ) {
		$scenario_condition_comp = array();

		foreach ( (array) $scenario_value as $cond_key => $cond_value ) {

			if ( in_array( $cond_value , $scenario_condition_comp ) ) {
				unset($af_cp_scenario_condition_comp[ $scenario_key ][ $cond_key ]);
			} else {
				$scenario_condition_comp[] = $cond_value;
			}

		}
	}
	update_post_meta( $post_id, 'af_cp_scenario_condition_comp', $af_cp_scenario_condition_comp );
} else {
	update_post_meta( $post_id, 'af_cp_scenario_condition_comp', array() );
}

if ( isset( $_POST['af_cp_scenario_action_comp'] ) ) {
	$af_cp_scenario_action_comp = sanitize_meta( '' , wp_unslash( $_POST['af_cp_scenario_action_comp'] ) , '' );

	foreach ( $af_cp_scenario_action_comp as $scenario_key => $scenario_value ) {
		$scenario_action_comp = array();

		foreach ( (array) $scenario_value as $action_key => $action_value ) {

			if ( in_array( $action_value , $scenario_action_comp ) ) {
				unset($af_cp_scenario_action_comp[ $scenario_key ][ $action_key ]);
			} else {
				$scenario_action_comp[] = $action_value;
			}
		}
	}
	update_post_meta( $post_id, 'af_cp_scenario_action_comp', $af_cp_scenario_action_comp );
} else {
	update_post_meta( $post_id, 'af_cp_scenario_action_comp', array() );
}

if ( isset( $_POST['af_cp_action_type'] ) ) {
	update_post_meta( $post_id, 'af_cp_action_type', sanitize_meta( '' , wp_unslash( $_POST['af_cp_action_type'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_action_type', array() );
}

if ( isset( $_POST['af_cp_sc_type_selection'] ) ) {
	update_post_meta( $post_id, 'af_cp_sc_type_selection', sanitize_meta( '' , wp_unslash( $_POST['af_cp_sc_type_selection'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_sc_type_selection', array() );
}

if ( isset( $_POST['af_cp_scenario_condition_type'] ) ) {
	update_post_meta( $post_id, 'af_cp_scenario_condition_type', sanitize_meta( '' , wp_unslash( $_POST['af_cp_scenario_condition_type'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_scenario_condition_type', array() );
}

if ( isset( $_POST['af_cp_scenario_condition_products'] ) ) {
	update_post_meta( $post_id, 'af_cp_scenario_condition_products', sanitize_meta( '' , wp_unslash( $_POST['af_cp_scenario_condition_products'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_scenario_condition_products', array() );
}

if ( isset( $_POST['af_cp_scenario_action_type'] ) ) {
	update_post_meta( $post_id, 'af_cp_scenario_action_type', sanitize_meta( '' , wp_unslash( $_POST['af_cp_scenario_action_type'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_scenario_action_type', array() );
}

if ( isset( $_POST['af_cp_scenario_action_products'] ) ) {
	update_post_meta( $post_id, 'af_cp_scenario_action_products', sanitize_meta( '' , wp_unslash( $_POST['af_cp_scenario_action_products'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_scenario_action_products', array() );
}

if ( isset( $_POST['af_cp_sc_action_type_selection'] ) ) {
	update_post_meta( $post_id, 'af_cp_sc_action_type_selection', sanitize_meta( '' , wp_unslash( $_POST['af_cp_sc_action_type_selection'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_sc_action_type_selection', array() );
}

if ( isset( $_POST['af_comp_product_layout'] ) ) {
	update_post_meta( $post_id, 'af_comp_product_layout', sanitize_meta( '' , wp_unslash( $_POST['af_comp_product_layout'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_comp_product_layout', array() );
}

if ( isset( $_POST['af_cp_selection_title_cb'] ) ) {
	update_post_meta( $post_id, 'af_cp_selection_title_cb', sanitize_meta( '' , wp_unslash( $_POST['af_cp_selection_title_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_selection_title_cb', array() );
}

if ( isset( $_POST['af_cp_selection_desc_cb'] ) ) {
	update_post_meta( $post_id, 'af_cp_selection_desc_cb', sanitize_meta( '' , wp_unslash( $_POST['af_cp_selection_desc_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_selection_desc_cb', array() );
}

if ( isset( $_POST['af_cp_selection_thumbnail_cb'] ) ) {
	update_post_meta( $post_id, 'af_cp_selection_thumbnail_cb', sanitize_meta( '' , wp_unslash( $_POST['af_cp_selection_thumbnail_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_selection_thumbnail_cb', array() );
}

if ( isset( $_POST['af_cp_selection_price_cb'] ) ) {
	update_post_meta( $post_id, 'af_cp_selection_price_cb', sanitize_meta( '' , wp_unslash( $_POST['af_cp_selection_price_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_selection_price_cb', array() );
}

if ( isset( $_POST['af_cp_selection_stock_cb'] ) ) {
	update_post_meta( $post_id, 'af_cp_selection_stock_cb', sanitize_meta( '' , wp_unslash( $_POST['af_cp_selection_stock_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_selection_stock_cb', array() );
}

if ( isset( $_POST['af_cp_comp_adj_type'] ) ) {
	update_post_meta( $post_id, 'af_cp_comp_adj_type', sanitize_meta( '' , wp_unslash( $_POST['af_cp_comp_adj_type'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_comp_adj_type', array() );
}

if ( isset( $_POST['af_cp_comp_adj_price_value'] ) ) {
	update_post_meta( $post_id, 'af_cp_comp_adj_price_value', sanitize_meta( '' , wp_unslash( $_POST['af_cp_comp_adj_price_value'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_comp_adj_price_value', array() );
}

if ( isset( $_POST['af_composite_product_sorting_cb'] ) ) {
	update_post_meta( $post_id, 'af_composite_product_sorting_cb', sanitize_meta( '' , wp_unslash( $_POST['af_composite_product_sorting_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_composite_product_sorting_cb', array() );
}

if ( isset( $_POST['af_cp_enable_scenario_cb'] ) ) {
	update_post_meta( $post_id, 'af_cp_enable_scenario_cb', sanitize_meta( '' , wp_unslash( $_POST['af_cp_enable_scenario_cb'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_enable_scenario_cb', array() );
}

if ( isset( $_POST['af_cp_sc_hide_comps'] ) ) {
	update_post_meta( $post_id, 'af_cp_sc_hide_comps', sanitize_meta( '' , wp_unslash( $_POST['af_cp_sc_hide_comps'] ) , '' ) );
} else {
	update_post_meta( $post_id, 'af_cp_sc_hide_comps', array() );
}
