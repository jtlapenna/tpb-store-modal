<?php
defined( 'ABSPATH' ) || exit;
	$description      = '';

	$af_cp_comp_default_img='';

	
	$af_cp_default_image_keys = (array) array_keys($af_cp_comp_image);
if (in_array( $comp_key , $af_cp_default_image_keys )) {

	$af_cp_comp_default_img=wp_get_attachment_url($af_cp_comp_image[ $comp_key ]);
}
		
	$description_keys = (array) array_keys($af_cp_comp_desc);
if ( in_array( $comp_key , $description_keys ) ) {
	$description = $af_cp_comp_desc[ $comp_key ];
}

	$list_style      = 'simple';
	$list_style_keys = (array) array_keys($af_cp_component_style);
if ( in_array( $comp_key , $list_style_keys ) ) {
	$list_style = $af_cp_component_style[ $comp_key ];
}

	$allow_sor      = '';
	$allow_sor_keys = (array) array_keys($af_cp_sorting_cb);
if ( in_array( $comp_key , $allow_sor_keys ) ) {
	$allow_sor = $af_cp_sorting_cb[ $comp_key ];
}
	$hidden_class     = '';
	$expand_html_text = '&#9650;';
	++$count;
if ( 1 < $count ) {
	$expand_html_text = '&#9660;';
	$hidden_class     = ' af_cp_toggle_template_hidden ';
}

?>
	<div class="single_component af-cp-toggle-product-<?php echo esc_attr( $product_id . '_' . $comp_key); ?> single_component_title af-cp-toggle-title-div" data-desc="single_comp_desc_<?php echo esc_attr($comp_key); ?>"  data-comp_key="<?php echo esc_attr($comp_key); ?>" >
		<div class="expand">
			<?php echo wp_kses_post($expand_html_text); ?>
		</div>
		<div class="title">
			<span><?php echo esc_html__( $comp_name , 'af_comp_product'); ?></span>
		</div>
	</div>
	<div class="single_component af-cp-toggle-product-<?php echo esc_attr( $product_id . '_' . $comp_key); ?> single_component_description single_comp_desc_<?php echo esc_attr($comp_key) . ' ' . esc_attr($hidden_class); ?>"  data-comp_key="<?php echo esc_attr($comp_key); ?>" >
		
	<?php
	if (!empty($af_cp_comp_default_img)) {
		echo '<img style="width:100%;height:auto;" src="' . esc_url($af_cp_comp_default_img) . '" alt="Image" />';
	}
	?>
	<div class="desc">
			<p><?php echo esc_html__( $description , 'af_comp_product'); ?></p>
		</div>
		<?php
		if ( 'yes' == $allow_sor ) {
			$this->af_cp_sorting_div( $product_id , $comp_key );
		}
		?>
		<div class="products single_products_<?php echo esc_attr($product_id . '_' . $comp_key ); ?>">
			<?php require AFCPB_DIR_PATH . '/templates/single-product/af-cp-products.php'; ?>
		</div>
		<div class="selected_product af_cp_selected_product_<?php echo esc_attr($product_id); ?>_<?php echo esc_attr($comp_key); ?>">
			<?php
				$default_product = af_cp_component_default_product( $product_id , $comp_key );
			if ( ( 0 != $default_product ) && ( wc_get_product($default_product) ) ) {
				if ( !in_array( $default_product , $comp_products ) ) {
					$default_product = current($comp_products);
				}
				af_cp_selected_product_html( $comp_products , $product_id , $default_product , $comp_key , array(), array() );
			}
			?>
		</div>
		<div class="af_cp_comp_messages af_cp_component_product_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>_errors"></div>
	</div>
	<?php
