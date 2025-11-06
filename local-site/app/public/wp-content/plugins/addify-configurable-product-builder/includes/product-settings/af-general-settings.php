<?php
defined( 'ABSPATH' ) || exit;
$af_comp_product_layout = get_post_meta( get_the_ID() , 'af_comp_product_layout' , true );
if ( ( 'vertical' != $af_comp_product_layout ) && ( 'toggle' != $af_comp_product_layout ) && ( 'steps' != $af_comp_product_layout ) ) {
	update_post_meta( get_the_ID() , 'af_comp_product_layout' , 'vertical' );
}
$af_comp_product_layout           = get_post_meta( get_the_ID() , 'af_comp_product_layout' , true );
$af_comp_product_price_type       = get_post_meta( get_the_ID() , 'af_comp_product_price_type' , true );
$af_cp_form_location              = get_post_meta( get_the_ID() , 'af_cp_form_location' , true );
$af_comp_product_price_adjustment = get_post_meta( get_the_ID() , 'af_comp_product_price_adjustment' , true );
$af_comp_product_adj_value        = get_post_meta( get_the_ID() , 'af_comp_product_adj_value' , true );

$af_comp_product_adj_max_qty = get_post_meta( get_the_ID() , 'af_comp_product_adj_max_qty' , true );
$af_comp_product_adj_min_qty = get_post_meta( get_the_ID() , 'af_comp_product_adj_min_qty' , true );
if ( ( '' == $af_comp_product_adj_min_qty ) || ( 0 == $af_comp_product_adj_min_qty ) ) {
	update_post_meta( get_the_ID() , 'af_comp_product_adj_min_qty' , '1' );
}
$af_comp_product_adj_min_qty  = get_post_meta( get_the_ID() , 'af_comp_product_adj_min_qty' , true );
$af_comp_product_adj_step_qty = get_post_meta( get_the_ID() , 'af_comp_product_adj_step_qty' , true );
if ( ( '' == $af_comp_product_adj_step_qty ) || ( 0 == $af_comp_product_adj_step_qty ) ) {
	update_post_meta( get_the_ID() , 'af_comp_product_adj_step_qty' , '1' );
}
$af_comp_product_adj_step_qty = get_post_meta( get_the_ID() , 'af_comp_product_adj_step_qty' , true );
$af_comp_product_text         = get_post_meta( get_the_ID() , 'af_comp_product_text' , true );
$af_cp_allow_edit_in_cart     = get_post_meta( get_the_ID() , 'af_cp_allow_edit_in_cart' , true );


?>
<table class="af-comp-product-tbl">
	<tr>
		<td>
			<?php echo esc_html__('Select a layout' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">
			<div class="af_cp_layout_main_div">
				<div class="af_cp_layout_div af_comp_product_layout_vertical 
				<?php 

				if ( 'vertical' == $af_comp_product_layout ) {
					echo ' af_cp_selected_layout '; 
				} 
				?>
				">
					<input type="radio" name="af_comp_product_layout" class="af_comp_product_layout" <?php checked( $af_comp_product_layout , 'vertical' ); ?> id="af_comp_product_layout_vertical" value="vertical" style="display:none;">
						<img src="<?php echo esc_url( AFCPB_DIR_URL . '/includes/assets/images/Vertical.png' ); ?>" >
						<span><?php echo esc_html__( 'Vertical' , 'af_comp_product' ); ?></span>
				</div>

				<div class="af_cp_layout_div af_comp_product_layout_toggle 
				<?php 
				if ( 'toggle' == $af_comp_product_layout ) {
					echo 'af_cp_selected_layout'; } 
				?>
				">
					<input type="radio" name="af_comp_product_layout" class="af_comp_product_layout" <?php checked( $af_comp_product_layout , 'toggle' ); ?> id="af_comp_product_layout_toggle" value="toggle" style="display:none;">
						<img src="<?php echo esc_url( AFCPB_DIR_URL . '/includes/assets/images/Toggle-design.png' ); ?>" >
						<span><?php echo esc_html__( 'Toggle' , 'af_comp_product' ); ?></span>
				</div>

				<div class="af_cp_layout_div af_comp_product_layout_steps 
				<?php 
				if ( 'steps' == $af_comp_product_layout ) {
					echo ' af_cp_selected_layout '; } 
				?>
				">
					<input type="radio" name="af_comp_product_layout" class="af_comp_product_layout" <?php checked( $af_comp_product_layout , 'steps' ); ?> id="af_comp_product_layout_steps" value="steps" style="display:none;">
						<img src="<?php echo esc_url( AFCPB_DIR_URL . '/includes/assets/images/Design-3.png' ); ?>" >
						<span><?php echo esc_html__( 'Steps' , 'af_comp_product' ); ?></span>
				</div>

			</div>
			<p class="description"><?php echo esc_html__( 'Select layout for configurable product.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Form location' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">

			<select name="af_cp_form_location" class="af-comp-product-input">
				<option value="after_desc" <?php selected( $af_cp_form_location , 'after_desc' ); ?> ><?php echo esc_html__('After Product Description' , 'af_comp_product'); ?></option>
				<option value="after_sum" <?php selected( $af_cp_form_location , 'after_sum' ); ?> ><?php echo esc_html__('After Product summary' , 'af_comp_product'); ?></option>
			</select>

			<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Select location for configurable product.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php echo esc_html__('Price type' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">

			<select name="af_comp_product_price_type" class="af-comp-product-input">
				<option value="fixed_price" <?php selected( $af_comp_product_price_type , 'fixed_price' ); ?> ><?php echo esc_html__('Fixed price for whole configurable product' , 'af_comp_product'); ?></option>
				<option value="calculated_price" <?php selected( $af_comp_product_price_type , 'calculated_price' ); ?> ><?php echo esc_html__('Calculated price from components' , 'af_comp_product'); ?></option>
			</select>
			<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Select price type for this configurable product.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Final price adjustment' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">

			<select name="af_comp_product_price_adjustment" class="af-comp-product-input af_cp_adj_type_select">
				<option value="ignore" <?php selected( $af_comp_product_price_adjustment , 'ignore' ); ?> ><?php echo esc_html__('Ignore adjustment' , 'af_comp_product'); ?></option>
				<option value="fixed_increase" <?php selected( $af_comp_product_price_adjustment , 'fixed_increase' ); ?> ><?php echo esc_html__('Fixed increase' , 'af_comp_product'); ?></option>
				<option value="fixed_decrease" <?php selected( $af_comp_product_price_adjustment , 'fixed_decrease' ); ?> ><?php echo esc_html__('Fixed decrease' , 'af_comp_product'); ?></option>
				<option value="percentage_increase" <?php selected( $af_comp_product_price_adjustment , 'percentage_increase' ); ?> ><?php echo esc_html__('Percentage increase' , 'af_comp_product'); ?></option>
				<option value="percentage_decrease" <?php selected( $af_comp_product_price_adjustment , 'percentage_decrease' ); ?> ><?php echo esc_html__('Percentage decrease' , 'af_comp_product'); ?></option>
			</select>

			<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Select adjustment type. This adjustment will apply to total price of composite product.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Adjustment value' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">

			<input type="number" min="0" class="af-comp-product-input af_cp_adj_value_input" name="af_comp_product_adj_value" value="<?php echo esc_attr( $af_comp_product_adj_value ); ?>" >
			<p class="description"><?php echo esc_html__( 'Enter Adjustment value ( price / percentage )' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Min quantity' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">

			<input type="number" class="af-comp-product-input" name="af_comp_product_adj_min_qty" min="1" value="<?php echo esc_attr( $af_comp_product_adj_min_qty ); ?>" >
			<p class="description"><?php echo esc_html__( 'Enter minimum quantity a customer can buy. Applies to whole configurable product.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Max quantity' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">

			<input type="number" class="af-comp-product-input" name="af_comp_product_adj_max_qty" min="1" value="<?php echo esc_attr( $af_comp_product_adj_max_qty ); ?>" >
			<p class="description"><?php echo esc_html__( 'Enter maximum quantity a customer can buy. Applies to whole configurable product.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Step quantity' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">
			<input type="number" class="af-comp-product-input" name="af_comp_product_adj_step_qty" min="1" value="<?php echo esc_attr( $af_comp_product_adj_step_qty ); ?>" >
			<p class="description"><?php echo esc_html__( 'Add quantity steps to sell this configurable products Multiple of X. This feature is not compatible with WooCommerce Blocks.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Allow edit in cart' , 'af_comp_product'); ?>
		</td>
		<td class="af_comp_product_grid_view">
			<input type="checkbox" class="" name="af_cp_allow_edit_in_cart" value="yes" <?php checked( $af_cp_allow_edit_in_cart , 'yes' , true ); ?> >
			<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Check if you want to allow user to edit component quantities in cart.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo esc_html__('Configurable product text ' , 'af_comp_product'); ?>
		</td>
		<td>
			<textarea name="af_comp_product_text" rows="40" cols="40" style="height: 100px; clear: both; width: 96%"><?php echo esc_attr($af_comp_product_text ); ?></textarea>
			<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Enter text for configurable product that will be shown to user on product page before components.' , 'af_comp_product' ); ?></p>
		</td>
	</tr>

</table>
