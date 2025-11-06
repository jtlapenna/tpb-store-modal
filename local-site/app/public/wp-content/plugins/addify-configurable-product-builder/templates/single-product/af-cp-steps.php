<?php
defined( 'ABSPATH' ) || exit;
$description = '';

$af_cp_comp_default_img='';

	
$af_cp_default_image_keys = (array) array_keys($af_cp_comp_image);
if (in_array( $comp_key , $af_cp_default_image_keys )) {

	$af_cp_comp_default_img=wp_get_attachment_url($af_cp_comp_image[ $comp_key ]);
}
	
if ( isset( $af_cp_comp_desc[ $comp_key ] ) ) {   
	$description = $af_cp_comp_desc[ $comp_key ];
}
$list_style = 'simple';
if ( isset($af_cp_component_style[ $comp_key ]) ) {
	$list_style = $af_cp_component_style[ $comp_key ];
}

$allow_sor = '';
if ( isset($af_cp_sorting_cb[ $comp_key ]) ) {
	$allow_sor = $af_cp_sorting_cb[ $comp_key ];
}
$hidden_class = '';
if ( $count > 0 ) {
	$hidden_class = 'af_cp_toggle_template_hidden';
}
++$count;
?>
	<div class="single_component 
	<?php 
	echo esc_attr( 'single_component_' . $product_id . '_' . $comp_key );
	echo ' single_comp_step_' . esc_attr($product_id . '_' . $comp_key );
	echo '  ' . esc_attr($hidden_class); 
	?>
	" data-comp_key="<?php echo esc_attr($comp_key); ?>" >
	<?php
	if (!empty($af_cp_comp_default_img)) {
		echo '<img style="width:100%;height:auto;" src="' . esc_url($af_cp_comp_default_img) . '" alt="Image" />';
	}
	?>
		<div class="title">
			<span><?php echo esc_html__( $comp_name , 'af_comp_product'); ?></span>
		</div>
		<div class="desc">
			<p><?php echo esc_html__( $description , 'af_comp_product'); ?></p>
		</div>
		<?php
		// add sorting filter later
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
				af_cp_selected_product_html( $comp_products , $product_id , $default_product , $comp_key , array() , array() );
			}
			?>
		</div>
		<div class="af_cp_comp_messages af_cp_component_product_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>_errors"></div>
	</div>
		<?php
			$name_keys         = array_keys($af_cp_comp_name);
			$current_key_index = array_search( $comp_key , $name_keys );
			$last_key_index    = end($name_keys);
			$prev_key_index    = '';
		if ( 0 <= ( $current_key_index - 1 ) ) {
			$prev_key_index = $name_keys[ $current_key_index - 1 ];
		}
			$next_key_index = '';
		if ( count( $name_keys ) > ( $current_key_index + 1 ) ) {
			$next_key_index = $name_keys[ $current_key_index + 1 ];
		}
			$review_layout = false;
		if ( count($name_keys) == ( $current_key_index + 1 ) ) {
			$review_layout = true;
		}
		?>
	<div data-last="" class="af_cp_steps_div 
	<?php 
	if ( $comp_key == $last_key_index ) {
		echo ' af_cp_last_step ';
	} if ($review_layout) {
		echo ' single_comp_step_review ';
	}  echo 'single_comp_step bottom_btn_step_' . esc_attr( $product_id . '_' . $comp_key ) . ' single_comp_step_' . esc_attr( $product_id . '_' . $comp_key ) . ' single_comp_step_' . esc_attr($product_id . '_' . $comp_key ) . '_btn ' . esc_attr($hidden_class); 
	?>
	">
		<div class="left">
			<?php if ( ( '' != $prev_key_index ) && ( isset($af_cp_comp_name[ $prev_key_index ]) ) ) { ?>
				<a data-hidden_from_scenario="no" class="af_cp_steps_btn af_cp_steps_btn_left " data-hide_rev="" data-prod_comp_key="<?php echo esc_attr($product_id . '_' . $prev_key_index ); ?>" data-val="single_comp_step_<?php echo esc_attr($product_id . '_' . $prev_key_index ); ?>"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;<?php echo esc_html__( $af_cp_comp_name[ $prev_key_index ] , 'af_comp_product'); ?></a>
			<?php } ?>
		</div>
		<div class="right">
			<?php if ( ( '' != $next_key_index ) && ( isset($af_cp_comp_name[ $next_key_index ]) ) ) { ?>
				<a data-hidden_from_scenario="no" class="af_cp_steps_btn af_cp_steps_btn_right " data-hide_rev="" data-prod_comp_key="<?php echo esc_attr($product_id . '_' . $prev_key_index ); ?>" data-val="single_comp_step_<?php echo esc_attr($product_id . '_' . $next_key_index ); ?>" ><?php echo esc_html__( $af_cp_comp_name[ $next_key_index ] , 'af_comp_product'); ?>&nbsp;&nbsp;<i class="fas fa-arrow-right"></i></a>
			<?php } elseif ( $review_layout ) { ?>
				<a data-hidden_from_scenario="no" class="af_cp_steps_btn af_cp_steps_btn_right single_comp_step_review_btn" data-hide_rev="" data-prod_comp_key="<?php echo esc_attr($product_id . '_' . $prev_key_index ); ?>" data-val="single_comp_step_review" ><?php echo esc_html__( 'Review your selection' , 'af_comp_product'); ?>&nbsp;&nbsp;<i class="fas fa-arrow-right"></i></a>
			<?php } ?>
		</div>
	</div>
	<?php
