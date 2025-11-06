<?php
defined( 'ABSPATH' ) || exit;

$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
$available_variations = $get_variations ? $product->get_available_variations() : false;
$attributes           = $product->get_variation_attributes();
$selected_attributes  = $product->get_default_attributes();

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

?>
<div class="product content  summary_content populated cart variations_form af_cp_variable_product_div" enctype='multipart/form-data' data-af_cp_key="<?php echo esc_attr($comp_key); ?>" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo esc_html__( $variations_attr); ?>" data-comp_key="<?php echo esc_attr($comp_key); ?>" >
	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php 
				foreach ( $attributes as $attribute_name => $options ) : 
					$selected_attribute      = '';
					$selected_attribute_name = strtolower( 'attribute_' . $attribute_name );
					if ( isset( $form_table[ $selected_attribute_name ] ) ) {
						$attribute_sel_keys = (array) array_keys( (array) $form_table[ $selected_attribute_name ]);
						if ( in_array( $comp_key , $attribute_sel_keys ) ) {
							$selected_attribute = $form_table[ $selected_attribute_name ][ $comp_key ];
						}
					}
					if ( isset( $_REQUEST[ $selected_attribute_name ] ) ) {
						$attribute_sel_keys = (array) array_keys( (array) sanitize_meta('', $_REQUEST[ $selected_attribute_name ] , '' ) );
						if ( isset($_REQUEST[ $selected_attribute_name ][ $comp_key ]) ) {
							$selected_attribute =  sanitize_meta('', $_REQUEST[ $selected_attribute_name ][ $comp_key ] , '' );
						}
					}
					?>
							<tr>
								<td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html__(wc_attribute_label( $attribute_name )); ?></label></td>
								<td class="value">
							<?php
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'class'     => 'af_cp_var_attr_select',
										'name'      => strtolower( 'attribute_' . $attribute_name ) . '[' . $comp_key . ']',
										'attribute' => $attribute_name,
										'product'   => $product,
										'selected'  => $selected_attribute,
									)
								);
								echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
							?>
								</td>
							</tr>
						<?php
					endforeach;
				?>
			</tbody>
		</table>

		<div class="single_variation_wrap">
			<p class="woocommerce-info af-cp-choose-attr-<?php echo esc_attr($comp_key); ?>"><?php echo esc_html__('Please choose product variations', 'af_comp_product'); ?></p>
			<div class="woocommerce-variation-add-to-cart variations_button">
				<label class="addify_product_on_backorder_field" style="display:none;"><?php echo esc_html__('Available on Backorder!', 'af_comp_product'); ?></label>
				<label class="lab_out_message" style="display: none;"><?php echo esc_html__('Out of Stock!', 'af_comp_product'); ?></label>
				<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
				<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
				<input type="hidden" name="variation_id" value="<?php echo absint($comp_key ); ?>" />
				<input type="hidden" name="af_cp_variation_id[<?php echo esc_attr($comp_key); ?>]" class="variation_id" value="0" />
				<div class="selected_attributes"></div>
			</div> 
			<div class="woocommerce-variation single_variation"></div>
		</div>
	<?php endif; ?>

</div>
<?php
