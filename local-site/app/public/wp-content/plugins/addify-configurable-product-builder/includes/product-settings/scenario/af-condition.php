<?php
defined( 'ABSPATH' ) || exit;
$component_name                    = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
$af_cp_sc_type_selection           = (array) get_post_meta( $product_id , 'af_cp_sc_type_selection' , true );
$af_cp_scenario_condition_type     = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_type' , true );
$af_cp_scenario_condition_products = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_products' , true );

$af_composite_product_all_products = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );
$af_composite_product_categories   = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
$af_composite_product_tags         = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );

$all_products = array();

if ( array_key_exists( $selected , (array) $af_composite_product_all_products ) ) {
	$all_products = (array) $af_composite_product_all_products[ $selected ];
}
$all_tags = array();

if ( array_key_exists( $selected , (array) $af_composite_product_tags ) ) {
	$all_tags = $af_composite_product_tags[ $selected ];
}

$all_cats = array();

if ( array_key_exists( $selected , (array) $af_composite_product_categories ) ) {
	$all_cats = $af_composite_product_categories[ $selected ];
}

$array_all_products                 = merge_all_products( $all_products , $all_cats , $all_tags );
	$selected_products              = array();
	$keys_for_sel_products_scenario = (array) array_keys( (array) $af_cp_scenario_condition_products);

if ( in_array( $key , $keys_for_sel_products_scenario ) ) {
	$sel_products_for_scenario = (array) array_keys( (array) $af_cp_scenario_condition_products[ $key ]);

	if ( isset($af_cp_scenario_condition_products[ $key ][ $condition_key ]) ) {
		$selected_products = (array) $af_cp_scenario_condition_products[ $key ][ $condition_key ];
	}
}

if ( 'ajax' == $scenario_ajax ) {
	$selected_products = array();
}
	
?>
<tr>
	<td>
		<select name="af_cp_scenario_condition_comp[<?php echo esc_attr($key); ?>][<?php echo esc_attr($condition_key); ?>]" data-products="af_cp_sc_products_<?php echo esc_attr($key . '_' . $condition_key ); ?>" data-sce_id="<?php echo esc_attr($key); ?>" data-condition_id="<?php echo esc_attr($condition_key); ?>" data-product_id="<?php echo esc_attr($product_id); ?>" class="af_cp_scenario_condition_comp af_cp_sc_component">
			<?php

			foreach ( $component_name as $comp_key => $comp_value ) {

				if ( '' == $selected ) {
					$selected = $comp_key;
				}

				if ( '' == $comp_value ) {
					$comp_value = '(UnKnown)';
				}

				?>
						<option value="<?php echo esc_attr($comp_key); ?>"  <?php selected( $selected , $comp_key ); ?> ><?php echo esc_html__( $comp_value , 'af_comp_product'); ?></option>
					<?php
			}
			?>
		</select>
	</td>
	<td>
		<select class="af_cp_sc_type_selection" name="af_cp_scenario_condition_type[<?php echo esc_attr($key); ?>][<?php echo esc_attr($condition_key); ?>]">
			<option value="is" <?php echo esc_attr(af_cp_selected_condition( $af_cp_scenario_condition_type , $key , $condition_key , 'is' )); ?> ><?php echo esc_html__( 'Is' , 'af_comp_product'); ?></option>
			<option value="not" <?php echo esc_attr(af_cp_selected_condition( $af_cp_scenario_condition_type , $key , $condition_key , 'not' )); ?> ><?php echo esc_html__( 'Not' , 'af_comp_product'); ?></option>
			<option value="any" <?php echo esc_attr(af_cp_selected_condition( $af_cp_scenario_condition_type , $key , $condition_key , 'any' )); ?> ><?php echo esc_html__( 'Any' , 'af_comp_product'); ?></option>
		</select>
	</td>
	<td>
		<select name="af_cp_scenario_condition_products[<?php echo esc_attr($key); ?>][<?php echo esc_attr($condition_key); ?>][]" class="af_cp_sc_all_products af_cp_simple_select2 af_cp_sc_products_<?php echo esc_attr($key . '_' . $condition_key ); ?>" style="width:100%; height:40px;" multiple 
		<?php 

		if ( 'selected' == af_cp_selected_condition( $af_cp_scenario_condition_type , $key , $condition_key , 'any' ) ) {
			echo esc_attr('disabled');
		}

		?>
			>
			<?php

			foreach ( $array_all_products as $products_in_component ) {

				if ( wc_get_product($products_in_component) ) {
					?>
						<option value="<?php echo esc_attr($products_in_component); ?>"
						<?php

						if ( in_array( $products_in_component , $selected_products ) ) {
							echo 'selected';
						}

						?>
							>
						<?php echo esc_html__( get_the_title($products_in_component) , 'af_comp_product'); ?>
						</option>
						<?php
				}
			}
			?>
		</select>
	</td>
	<td>
		<span class="af_cp_remove_row dashicons dashicons-trash"></span>
	</td>
</tr>
<?php
