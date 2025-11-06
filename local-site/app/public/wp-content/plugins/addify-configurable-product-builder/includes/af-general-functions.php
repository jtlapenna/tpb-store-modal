<?php
defined( 'ABSPATH' ) || exit;
if ( !function_exists( 'afcpb_standard_number' ) ) {
	function afcpb_standard_number( $price ) {
		$args  = array();
		$args  = apply_filters(
			'wc_price_args',
			wp_parse_args(
			$args,
			array(
				'ex_tax_label'       => false,
				'currency'           => '',
				'decimal_separator'  => wc_get_price_decimal_separator(),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimals'           => wc_get_price_decimals(),
				'price_format'       => get_woocommerce_price_format(),
			)
			)
		);

		$price = (float) $price;
			
		$unformatted_price = $price;
		$negative          = $price < 0;
			
		$price = apply_filters( 'raw_woocommerce_price', $negative ? $price * -1 : $price, $price );
			
		$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'], $price );
			
		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
			$price = wc_trim_zeros( $price );
		}
		$price = get_woocommerce_currency_symbol() . $price;
		return $price;
	}
}


if ( !function_exists( 'af_cp_selected_product_html' ) ) {
	
	function af_cp_selected_product_html( $comp_products, $current_product_id, $product_id, $comp_key, $form_table, $form_variation_ids ) {

		$af_cp_quantity_op = (array) get_post_meta( $current_product_id , 'af_composite_product_quantity_op' , true );
		$qty_option        = '';

		if ( isset($af_cp_quantity_op[ $comp_key ]) ) {
			$qty_option = $af_cp_quantity_op[ $comp_key ];
		}
		$fixed_qty_text = '';
		$fixed_qty      = 1;

		if ( 'fixed' == $qty_option ) {
			$fixed_qty_op = (array) get_post_meta( $current_product_id , 'af_composite_product_fixed_qty' , true );

			if ( isset($fixed_qty_op[ $comp_key ]) ) {
				$fixed_qty = $fixed_qty_op[ $comp_key ];
			}

			if ( '' == $fixed_qty ) {
				$fixed_qty = 1;
			}
			$fixed_qty_text = $fixed_qty . ' &times; ';
		}

		
		
		$price_for_del= '';
	
		if ( !empty($comp_products) ) {
			if ( !in_array( $product_id , (array) $comp_products ) ) {
				$product_id = current( (array) $comp_products);
			}
		}

		$component_style = get_post_meta( $current_product_id , 'af_composite_product_component_style' , true );
		if ( isset($component_style[ $comp_key ]) ) {
			if ( ( 'thumbnail' == $component_style[ $comp_key ] ) || ( 'radio' == $component_style[ $comp_key ] ) ) {
				$current_product_obj = wc_get_product( $product_id );
				if ( !$current_product_obj->is_type('variable') ) {             
					return '';
				}
			}
		}

		$image = '';
		if ( is_array(wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' )) ) {
			$image = current( (array) wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' ));
		}
		if ( '' == $image ) {
			$prod_image_id = get_post_meta( $current_product_id , 'af_comp_product_component_image' , true );
			if ( isset($prod_image_id[ $comp_key ]) ) {
				if ( is_array(wp_get_attachment_image_src( (int) $prod_image_id[ $comp_key ] )) ) {
					$image = current( (array) wp_get_attachment_image_src( (int) $prod_image_id[ $comp_key ] ) );
				}
			}
		}
		$product        = wc_get_product($product_id);
		$show_title_arr = (array) get_post_meta( $current_product_id , 'af_cp_selection_title_cb' , true );
		$title_keys     = (array) array_keys( $show_title_arr );
		$show_title     = '';
		if ( in_array( $comp_key , $title_keys ) ) {
			$show_title = $show_title_arr[ $comp_key ];
		}
			
		$show_desc_arr = (array) get_post_meta( $current_product_id , 'af_cp_selection_desc_cb' , true );
		$desc_keys     = (array) array_keys( $show_desc_arr );
		$show_desc     = '';
		if ( in_array( $comp_key , $desc_keys ) ) {
			$show_desc = $show_desc_arr[ $comp_key ];
		}
			
		$show_thumbnail_arr = (array) get_post_meta( $current_product_id , 'af_cp_selection_thumbnail_cb' , true );
		$thumbnail_keys     = (array) array_keys( $show_thumbnail_arr );
		$show_thumbnail     = '';
		if ( in_array( $comp_key , $thumbnail_keys ) ) {
			$show_thumbnail = $show_thumbnail_arr[ $comp_key ];
		}
			
		$show_price_arr = (array) get_post_meta( $current_product_id , 'af_cp_selection_price_cb' , true );
		$price_keys     = (array) array_keys( $show_price_arr );
		$show_price     = '';
		if ( in_array( $comp_key , $price_keys ) ) {
			$show_price = $show_price_arr[ $comp_key ];
		}

		$show_stock_arr = (array) get_post_meta( $current_product_id , 'af_cp_selection_stock_cb' , true );
		$stock_keys     = (array) array_keys( $show_stock_arr );
		$show_stock     = '';
		if ( in_array( $comp_key , $stock_keys ) ) {
			$show_stock = $show_stock_arr[ $comp_key ];
		}
		if ( ( '' == $show_title ) && ( '' == $show_desc ) && ( '' == $show_price ) && ( '' == $show_stock ) && ( '' == $show_thumbnail ) ) {
			return '';
		}
		if ( '' == $product->get_price() ) {
			$price = '';
		} else {
			$price              = $product->get_price_html();
			$loop_regular_price = af_cp_calculate_reg_sale_prices( $product )['regular'];
			$loop_sale_price    = af_cp_calculate_reg_sale_prices( $product )['sale'];
					
			$regular_price = af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $loop_regular_price );
			$sale_price    = af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $loop_sale_price );
		
		}

		?>
			<div class="image">
			<?php
			if ( 'yes' == $show_thumbnail ) {
				if ( '' == $image ) {
					$image = AFCPB_DIR_URL . '/includes/assets/images/image-placeholder.jpg';
				}
				?>
				<img src="<?php echo esc_url($image); ?>" data-id="<?php echo esc_attr($product_id); ?>">
				<?php
			}
			?>
			</div>
			<div class="detail">
			<?php
			if ('yes' == $show_title) {
				?>
				 <h4 class="product_title"><?php echo esc_html__( get_the_title($product_id) , 'af_comp_product'); ?></h4>
				 <?php
			}
			if ( ( 'yes' == $show_price )&&( !( $product->is_type('variable') ) ) ) {
			
				$af_cp_parent_product_price_type=get_post_meta( $current_product_id , 'af_comp_product_price_type' , true );
				$af_cp_get_component_product_type=(array) get_post_meta( $current_product_id , 'af_cp_comp_adj_type' , true );

				if ('fixed_price'==$af_cp_parent_product_price_type) {

					?>
					<p class="af_cp_price"><del><?php echo wp_kses_post( wc_price($loop_regular_price)); ?></del>  <?php echo wp_kses_post( wc_price(0)); ?></p>
					<?php
					  
				} elseif ('calculated_price'==$af_cp_parent_product_price_type) {
					
					if ('same_price'==$af_cp_get_component_product_type[ $comp_key ]) {

						if (empty($product->get_sale_price())) {
							?>
							<p class="af_cp_price"><?php echo wp_kses_post( wc_price($loop_regular_price) ); ?></p>
							<?php
						} else {

							?>
							<p class="af_cp_price"><del><?php echo wp_kses_post(wc_price($loop_regular_price)); ?></del>  <?php echo wp_kses_post( wc_price($loop_sale_price) ); ?></p>
							<?php

						}

					} else if (( 'fixed_decrease'==$af_cp_get_component_product_type[ $comp_key ] )
						||( 'percentage_decrease'==$af_cp_get_component_product_type[ $comp_key ] )) {

						if (empty($product->get_sale_price())) {
							?>
							<p class="af_cp_price"><del><?php echo wp_kses_post(wc_price($loop_regular_price)); ?></del>  <?php echo wp_kses_post( wc_price($regular_price) ); ?></p>
							<?php
						} else {
							?>
							<p class="af_cp_price"><del><?php echo wp_kses_post(wc_price($loop_regular_price)); ?></del>  <?php echo wp_kses_post( wc_price($sale_price) ); ?></p>
							<?php
						}
					} elseif (empty($product->get_sale_price())) {

						?>
							<p class="af_cp_price"><?php echo wp_kses_post( wc_price($regular_price) ); ?></p>
						<?php
					} else {
						?>
							<p class="af_cp_price"><?php echo wp_kses_post( wc_price($sale_price) ); ?></p>
						<?php
						
					}

				}   

			}

			if ( 'yes' == $show_desc ) {
				?>
					<p><?php echo wp_kses_post( $product->get_short_description() ); ?></p>
					<?php
			}
			if ( 'yes' == $show_stock ) {
				?>
					<p class="stock_msg"><?php echo wp_kses_post( af_cp_get_stock_msg_with_qty($product_id) ); ?></p>
					<?php
			}
			
			if ( $product->is_type('variable') ) {


				$show_var_prices = $product->get_variation_prices( true );
				$var_min_price = $show_var_prices['price'] ? min( $show_var_prices['price'] ) : 0;
				$var_max_price = $show_var_prices['price'] ? max( $show_var_prices['price'] ) : 0;
		
				$var_min_price_html = wc_price( $var_min_price );
				$var_max_price_html = wc_price( $var_max_price );
		
				// Return the formatted price range
				echo '<p class="af_cp_closest_variable_price_range show_variable_product_range_' . esc_attr($comp_key) . '"> ' . wp_kses_post($var_min_price_html) . ' - ' . wp_kses_post($var_max_price_html) . ' </p>';

				if (( 'yes' == $show_price )) {
				
					if (!empty($form_variation_ids[ $comp_key ])) {  
				
					   $product_variation_object     = wc_get_product( $form_variation_ids[ $comp_key ] );

						if (!empty($product_variation_object)) {
				
							$price_variation              = $product_variation_object->get_price_html();
							$loop_regular_price_variation = af_cp_calculate_reg_sale_prices( $product_variation_object )['regular'];
							$loop_sale_price_variation    = af_cp_calculate_reg_sale_prices( $product_variation_object )['sale'];
					
							if (!empty($loop_regular_price_variation)) {
							
							$regular_price_variation = af_cp_component_product_price( $current_product_id , $form_variation_ids[ $comp_key ] , $comp_key , $loop_regular_price_variation );
							$sale_price_variation    = af_cp_component_product_price( $current_product_id , $form_variation_ids[ $comp_key ] , $comp_key , $loop_sale_price_variation );
						
	 
							 $af_cp_parent_product_price_type_variation=get_post_meta( $current_product_id , 'af_comp_product_price_type' , true );
							 $af_cp_get_component_product_type_variation=(array) get_post_meta( $current_product_id , 'af_cp_comp_adj_type' , true );
			 
								if ('fixed_price'==$af_cp_parent_product_price_type_variation) {
			 
							
									?>
								<p class="af_cp_price"><del><?php echo wp_kses_post( wc_price($loop_regular_price_variation)); ?></del>  <?php echo wp_kses_post( wc_price(0)); ?></p>
								<?php
			 
								   
								} elseif ('calculated_price'==$af_cp_parent_product_price_type_variation) {
								 
									if ('same_price'==$af_cp_get_component_product_type_variation[ $comp_key ]) {
			 
										if (empty($product_variation_object->get_sale_price())) {
											?>
										 <p class="af_cp_price"><?php echo wp_kses_post( wc_price($loop_regular_price_variation) ); ?></p>
											<?php
										} else {
											?>
										<p class="af_cp_price"><del><?php echo wp_kses_post( wc_price($loop_regular_price_variation)); ?></del>  <?php echo wp_kses_post( wc_price($loop_sale_price_variation)); ?></p>
										<?php
										}
			 
									} else if (( 'fixed_decrease'==$af_cp_get_component_product_type_variation[ $comp_key ] )
									 ||( 'percentage_decrease'==$af_cp_get_component_product_type_variation[ $comp_key ] )) {
			 
										if (empty($product_variation_object->get_sale_price())) {
											?>
										 <p class="af_cp_price"><del><?php echo wp_kses_post(wc_price($loop_regular_price_variation)); ?></del>  <?php echo wp_kses_post( wc_price($regular_price_variation) ); ?></p>
											<?php
										} else {
											?>
										<p class="af_cp_price"><del><?php echo wp_kses_post(wc_price($loop_regular_price_variation)); ?></del>  <?php echo wp_kses_post( wc_price($sale_price_variation) ); ?></p>
										<?php
										}
									} elseif (empty($product->get_sale_price())) {

										?>
										<p class="af_cp_price"><?php echo wp_kses_post( wc_price($regular_price_variation) ); ?></p>
										 <?php	                      
									} else {
										?>
										<p class="af_cp_price"><?php echo wp_kses_post( wc_price($sale_price_variation) ); ?></p>
										 <?php	
									}
								}   
							}
						}    
					}
				}   
				include AFCPB_DIR_PATH . '/templates/variable-product/af-variable-product-template.php';
			}
			af_cp_component_common_qty( $current_product_id , $qty_option , $comp_key , $fixed_qty );
			?>
			
			<div class="view_product_comp_compi desc" style="display:flex;width:100%!important;" >
		
		<?php
		if (!empty(get_option( 'wc_settings_tab_composite_product_af_cp_view_product' ))) {
			?>
			<a  href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" style="font-size:12px;margin-top:3px;" class="view_product" target="_blank">
					<?php echo esc_attr(get_option( 'wc_settings_tab_composite_product_af_cp_view_product' )); ?>
			</a> 
				<?php } ?>
		</p>
		
	</div>	

			</div>
		
			

	
<?php
	}
}

if ( !function_exists( 'af_cp_get_stock_msg_with_qty' ) ) {
	function af_cp_get_stock_msg_with_qty( $product_id ) {
		$product = wc_get_product( $product_id );
		$message = '';      

		if ( ( 'instock' == $product->get_stock_status() ) && ( 1 == $product->get_manage_stock() ) ) {
			$message = '<span class="af_cp_green">' . $product->get_stock_quantity() . ' ' . $product->get_stock_status() . '</span>';
		} else {
			$message = $product->get_stock_status();
			if ( 'outofstock' == $message ) {
				$message = '<span class="stock out-of-stock">' . $message . '</span>';
			}
			if ( 'instock' == $message ) {
				$message = '<span class="af_cp_green">' . $message . '</span>';
			}
		}
		return $message;
	}
}

if ( !function_exists( 'af_cp_get_comp_title' ) ) {
	function af_cp_get_comp_title( $product_id, $comp_key ) {
		$af_comp_product_component_name = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
		if ( isset($af_comp_product_component_name[ $comp_key ]) ) {
			return $af_comp_product_component_name[ $comp_key ];
		}
		return '';
	}
}

if ( !function_exists( 'af_cp_product_stock' ) ) {
	function af_cp_product_stock( $product_id ) {
		$product = wc_get_product($product_id);
		if ( !$product ) {
			$return_array = array(
				'type'  => 'no',
				'count' => '',
			);
			return $return_array;
		}
		$stock        =  $product->get_stock_quantity();
		$return_array = array(
			'type'  => 'no',
			'count' => $stock,
		);
		if ( ( '' == $product->managing_stock() ) && ( 'instock' == $product->get_stock_status() ) ) {
			$return_array = array(
				'type'  => 'yes',
				'count' => $stock,
			);
		}
		if ( 'onbackorder' == $product->get_stock_status() ) {
			$return_array = array(
				'type'  => 'yes',
				'count' => $stock,
			);
		}
		if ( ( 1 == $product->managing_stock() ) ) {
			$back_order_op = get_post_meta( $product->get_id(), '_backorders', true );
			if ( 'no' == $back_order_op ) {
				$return_array = array(
					'type'  => 'no',
					'count' => $stock,
				);
			}
		}
		return $return_array;
	}
}

if ( !function_exists( 'af_cp_component_product_price' ) ) {
	function af_cp_component_product_price( $parent_id, $product_id, $comp_key, $price ) {
		$adj_type_arr = (array) get_post_meta( $parent_id , 'af_cp_comp_adj_type' , true );
		$value_arr    = (array) get_post_meta( $parent_id , 'af_cp_comp_adj_price_value' , true );
		$value        = 0;
		$price        = floatval($price);
		$value_keys   = (array) array_keys( $value_arr );
		if ( in_array( $comp_key , $value_keys ) ) {
			$value = $value_arr[ $comp_key ];
			if ( ( '' != $value ) || ( 0 != $value ) ) {
				$adj_type      = '';
				$adj_type_keys = (array) array_keys( $adj_type_arr );
				if ( in_array( $comp_key , $adj_type_keys ) ) {
					$adj_type = $adj_type_arr[ $comp_key ];
				}
				$percentage = ( floatval($price) / 100 ) * floatval( $value );
				if ( 'fixed_increase' == $adj_type ) {
					$price = ( $price + floatval($value) );
				} elseif ( 'fixed_decrease' == $adj_type ) {
					$price = ( $price - floatval($value) );
				} elseif ( 'percentage_increase' == $adj_type ) {
					$price = ( $price + floatval($percentage) );
				} elseif ( 'percentage_decrease' == $adj_type ) {
					$price = ( $price - floatval($percentage) );
				} elseif ('same_price'== $adj_type) {
					$price=$price;
				}
			}
		}
		if ( 0 > $price ) {
			$price = 0;
		}
		return $price;
	}
}

if ( !function_exists( 'af_cp_calculate_general_price' ) ) {
	function af_cp_calculate_general_price( $product_id, $price ) {
		$adjust_type  = get_post_meta( $product_id , 'af_comp_product_price_adjustment' , true );
		$adjust_value = floatval(get_post_meta( $product_id , 'af_comp_product_adj_value' , true ));
		if ( ( 0 != $adjust_value ) && ( '' != $adjust_value ) ) {
			$percentage = ( $price/100 ) * $adjust_value;
			if ( 'fixed_increase' == $adjust_type ) {
				$price += $adjust_value;
			} elseif ( 'fixed_decrease' == $adjust_type ) {
				$price = $price - $adjust_value;
			} elseif ( 'percentage_increase' == $adjust_type ) {
				$price = $price + $percentage;
			} elseif ( 'percentage_decrease' == $adjust_type ) {
				$price = $price - $percentage;
			}
		}
		if ( 0 > $price ) {
			$price = 0;
		}
		return $price;
	}
}

if ( !function_exists( 'get_stock_msg_with_qty' ) ) {
	function get_stock_msg_with_qty( $product_id ) {
		$product = wc_get_product( $product_id );
		$message = '';
		if ( ( 'instock' == $product->get_stock_status() ) && ( 1 == $product->get_manage_stock() ) ) {
			$message = $product->get_stock_quantity() . ' ' . $product->get_stock_status();
		} else {
			$message = $product->get_stock_status();
		}
		return $message;
	}
}

if ( !function_exists( 'af_cp_price_in_option' ) ) {
	function af_cp_price_in_option( $current_product_id, $product_id, $comp_key ) {
		$product = wc_get_product( $product_id );
		$af_cp_parent_product_price_type=get_post_meta( $current_product_id , 'af_comp_product_price_type' , true );
		if ( '' == $product->get_price() ) {
			return '';
		}

		if ('variable'==$product->get_type()) {
			
			$show_var_prices = $product->get_variation_prices( true );
			$var_min_price = $show_var_prices['price'] ? min( $show_var_prices['price'] ) : 0;
			$var_max_price = $show_var_prices['price'] ? max( $show_var_prices['price'] ) : 0;
	
			$var_min_price_html = wc_price( $var_min_price );
			$var_max_price_html = wc_price( $var_max_price );

			return '<span class="show_template_product_range_' . $comp_key . '">' . $var_min_price_html . ' - ' . $var_max_price_html . '</span>';
	
		}

		$price_op_arr  = (array) get_post_meta( $current_product_id , 'af_composite_product_price_style' , true );
		$price_op_keys = (array) array_keys( $price_op_arr );
		$price_msg     = '';
		if ( in_array( $comp_key , $price_op_keys ) ) {
			$price_op = $price_op_arr[ $comp_key ];
			if ( 'active' == $price_op ) {
			
				if ('fixed_price'==$af_cp_parent_product_price_type) {
					
					$price_msg = wc_price(0);

				} elseif ('calculated_price'==$af_cp_parent_product_price_type) {

					$price_msg = wc_price( af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $product->get_price() ));
					
				}
			
			} elseif ( 'full_price' == $price_op ) {
				if ( $product->is_type('variable') ) {
					$regular_price = af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $product->get_variation_price('max') );
					$sale_price    = af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $product->get_price() );
				} else {
					
					$price_show              = $product->get_price_html();
					$loop_regular_price = af_cp_calculate_reg_sale_prices( $product )['regular'];
					$loop_sale_price    = af_cp_calculate_reg_sale_prices( $product )['sale'];
							
					$regular_price = af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $loop_regular_price );
					$sale_price    = af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $loop_sale_price );
					
				
					$af_cp_get_component_product_type=(array) get_post_meta( $current_product_id , 'af_cp_comp_adj_type' , true );
	
					if ('fixed_price'==$af_cp_parent_product_price_type) {
	
						$price_msg = '<del>' . wc_price( $loop_regular_price ) . '</del> <ins>' . wc_price(0) . '</ins>' ;
	
						  
					} elseif ('calculated_price'==$af_cp_parent_product_price_type) {
						
						if ('same_price'==$af_cp_get_component_product_type[ $comp_key ]) {
	
							if (empty($product->get_sale_price())) {
								$price_msg = wc_price( $loop_regular_price);
							} else {
								
								$price_msg = '<del>' . wc_price( $loop_regular_price) . '</del> <ins>' . wc_price($loop_sale_price) . '</ins>' ;        
							
							}
	
						} else if (( 'fixed_decrease'==$af_cp_get_component_product_type[ $comp_key ] )
							||( 'percentage_decrease'==$af_cp_get_component_product_type[ $comp_key ] )) {
	
							if (empty($product->get_sale_price())) {

								$price_msg = '<del>' . wc_price( $loop_regular_price ) . '</del> <ins>' . wc_price($regular_price) . '</ins>' ;

							
							} else {

								$price_msg = '<del>' . wc_price( $loop_regular_price ) . '</del> <ins>' . wc_price($sale_price) . '</ins>' ;

							}
						} elseif (empty($product->get_sale_price())) {

								$price_msg = wc_price($regular_price);
						} else {
							$price_msg = wc_price($sale_price);
						
						}
	
					}   
	
			
				}
			
			}
		}




		return $price_msg;
	}
}

if ( !function_exists( 'af_cp_stock_in_option' ) ) {
	function af_cp_stock_in_option( $current_product_id, $product_id, $comp_key ) {
		$stock_op_arr  = (array) get_post_meta( $current_product_id , 'af_composite_product_stock_cb' , true );
		$stock_op_keys = (array) array_keys( $stock_op_arr );
		$stock_msg     = '';
		$return_arr    = array(
			'type' => 'hide',
			'msg'  => '',
		);
		$disable       = '';
		$min_qty       = get_min_qty_component( $product_id , $comp_key );
		$stock_arr     = af_cp_product_stock( $product_id );
		if ( 'no' == $stock_arr['type'] ) {
			if ( $min_qty > $stock_arr['count'] ) {
				$disable = 'disable';
			}
		}
			
		if ( in_array( $comp_key , $stock_op_keys ) ) {
			$stock_op = $stock_op_arr[ $comp_key ];
			if ( 'both' == $stock_op ) {
				$return_arr = array(
					'type' => $disable,
					'msg'  => af_cp_get_stock_msg_with_qty( $product_id ),
				);
			} elseif ( 'disable' == $stock_op ) {
				$return_arr = array(
					'type' => $disable,
					'msg'  => '',
				);
			} elseif ( 'message' == $stock_op ) {
				$return_arr = array(
					'type' => 'show',
					'msg'  => af_cp_get_stock_msg_with_qty( $product_id ),
				);
			}
		}
		return $return_arr;
	}
}

if ( !function_exists( 'get_min_qty_component' ) ) {
	function get_min_qty_component( $product_id, $comp_key ) {
		$min      = 0;
		$min_arr  = (array) get_post_meta( $product_id , 'af_composite_product_min_qty' , true );
		$min_keys = (array) array_keys( $min_arr );
		if ( in_array( $comp_key , $min_keys ) ) {
			if ( $min_arr[ $comp_key ] ) {
				$min = $min_arr[ $comp_key ];
			}
		}
		return $min;
	}
}

if ( !function_exists( 'af_cp_component_default_product' ) ) {
	function af_cp_component_default_product( $product_id, $comp_key ) {
		
		$af_cp_default_product = (array) get_post_meta( $product_id , 'af_composite_product_default_product' , true );
		if ( isset( $_REQUEST['af_cp_component_product'] ) ) {
			if ( isset($_REQUEST['af_cp_component_product'][ $comp_key ]) ) {
				$default_product =  sanitize_meta('', $_REQUEST['af_cp_component_product'][ $comp_key ] , '' );
				return $default_product;
			}
		}
		$return_default_product = 0;
		if ( isset($af_cp_default_product[ $comp_key ]) ) {
			if ( wc_get_product($af_cp_default_product[ $comp_key ]) ) {
				if ( 'af_composite_product' != wc_get_product($af_cp_default_product[ $comp_key ])->get_type() ) {
					$return_default_product = $af_cp_default_product[ $comp_key ];
				}
			}
		}
		$comp_products = (array) af_cp_component_products( $product_id , $comp_key );
		if ( !in_array( $return_default_product , (array) $comp_products ) ) {
			$return_default_product = floatval( current( $comp_products ) );
		}
		return $return_default_product;
	}
}

// if ( !function_exists( 'af_cp_component_default_product' ) ) {
//  function af_cp_component_default_product( $product_id, $comp_key ) {
//      // Fetch the default product from post meta
//      $af_cp_default_product = (array) get_post_meta( $product_id, 'af_composite_product_default_product', true );

//      // Check if the request has a component product for the given key
//      if ( isset( $_REQUEST['af_cp_component_product'] ) ) {
//          if ( isset( $_REQUEST['af_cp_component_product'][ $comp_key ] ) ) {
//              $default_product = sanitize_meta( '', $_REQUEST['af_cp_component_product'][ $comp_key ], '' );
//              return $default_product;
//          }
//      }

//      $return_default_product = 0;

//      // Validate the default product from meta
//      if ( isset( $af_cp_default_product[ $comp_key ] ) ) {
//          $product = wc_get_product( $af_cp_default_product[ $comp_key ] );
//          if ( $product && 'af_composite_product' !== $product->get_type() ) {
//              $return_default_product = $af_cp_default_product[ $comp_key ];
//          }
//      }

//      // Get all products for the component
//      $comp_products = (array) af_cp_component_products( $product_id, $comp_key );

//      // Remove any composite products from the array
//      $comp_products = array_filter( $comp_products, function( $product_id ) {
//          $product = wc_get_product( $product_id );
//          return $product && 'af_composite_product' !== $product->get_type();
//      } );

//      // If the default product is not in the filtered component products, set it to the first available product
//      if ( !in_array( $return_default_product, $comp_products ) ) {
//          $return_default_product = !empty( $comp_products ) ? floatval( current( $comp_products ) ) : 0;
//      }

//      return $return_default_product;
//  }
// }



// if ( !function_exists( 'af_cp_component_default_product' ) ) {
//  function af_cp_component_default_product( $product_id, $comp_key ) {
//      // Fetch the default product from post meta
//      $af_cp_default_product = (array) get_post_meta( $product_id, 'af_composite_product_default_product', true );

//      // Check if the request has a component product for the given key
//      if ( !empty( $_REQUEST['af_cp_component_product'][ $comp_key ] ) ) {
//          return sanitize_meta( '', $_REQUEST['af_cp_component_product'][ $comp_key ], '' );
//      }

//      $return_default_product = 0;

//      // Validate the default product from meta
//      if ( !empty( $af_cp_default_product[ $comp_key ] ) ) {
//          $product = wc_get_product( $af_cp_default_product[ $comp_key ] );
//          if ( $product && 'af_composite_product' !== $product->get_type() ) {
//              $return_default_product = $af_cp_default_product[ $comp_key ];
//          }
//      }

//      // Get all products for the component
//      $comp_products = (array) af_cp_component_products( $product_id, $comp_key );

//      // Manually filter out composite products and variations with empty attributes
//      $valid_products = [];
//      foreach ( $comp_products as $product_id ) {
//          $product = wc_get_product( $product_id );

//          // Skip if the product is invalid or a composite product
//          if ( !$product || 'af_composite_product' === $product->get_type() ) {
//              continue;
//          }

//          // Skip if the product is a variation with empty attributes
//          if ( $product->is_type( 'variation' ) ) {
//              $variation_attributes = $product->get_variation_attributes();
//              $has_empty_attribute = false;
//              foreach ( $variation_attributes as $attribute ) {
//                  if ( empty( $attribute ) ) {
//                      $has_empty_attribute = true;
//                      break;
//                  }
//              }
//              if ( $has_empty_attribute ) {
//                  continue;
//              }
//          }

//          // Add valid products to the array
//          $valid_products[] = $product_id;
//      }

//      // Set the default product to the first valid product if the current one is invalid
//      if ( !in_array( $return_default_product, $valid_products, true ) ) {
//          $return_default_product = !empty( $valid_products ) ? (int) current( $valid_products ) : 0;
//      }

//      return $return_default_product;
//  }
// }






if ( !function_exists( 'get_max_qty_component' ) ) {
	function get_max_qty_component( $product_id, $comp_key ) {
		$max      = '';
		$max_arr  = (array) get_post_meta( $product_id , 'af_composite_product_max_qty' , true );
		$max_keys = (array) array_keys( $max_arr );
		if ( in_array( $comp_key , $max_keys ) ) {
			if ( $max_arr[ $comp_key ] ) {
				$max = $max_arr[ $comp_key ];
			}
		}
		return $max;
	}
}

if ( !function_exists( 'get_step_qty_component' ) ) {
	function get_step_qty_component( $product_id, $comp_key ) {
		$step      = 1;
		$step_arr  = (array) get_post_meta( $product_id , 'af_composite_product_step_qty' , true );
		$step_keys = (array) array_keys( $step_arr );
		if ( in_array( $comp_key , $step_keys ) ) {
			if ( $step_arr[ $comp_key ] ) {
				$step = $step_arr[ $comp_key ];
			}
		}
		return $step;
	}
}

if ( !function_exists( 'af_cp_required_comp' ) ) {
	function af_cp_required_comp( $product_id, $comp_key ) {
		$required_arr      = (array) get_post_meta( $product_id , 'af_composite_product_required_component' , true );
		$required_keys_arr = (array) array_keys( $required_arr );
		$return            = '';
		if ( in_array( $comp_key , $required_keys_arr ) ) {
			if ( 'yes' == $required_arr[ $comp_key ] ) {
				$return = 'required';
			}
		}
		return $return;
	}
}

if ( !function_exists( 'af_cp_calculate_reg_sale_prices' ) ) {
	function af_cp_calculate_reg_sale_prices( $product ) {
		$total_regular_price = $product->get_regular_price();
		$total_sale_price    = $product->get_price();
		if ( $product->is_type('variable') ) {
			$total_regular_price = $product->get_variation_price('max');
			$total_sale_price    = $product->get_variation_price('max');
		}

		$return = array(
			'regular' => $total_regular_price,
			'sale'    => $total_sale_price,
		);

		return $return;
	}
}

if ( !function_exists( 'af_cp_component_products' ) ) {
	function af_cp_component_products( $product_id, $comp_key ) {
		$af_comp_product_component_name    = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
		$af_composite_product_all_products = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );
		$af_composite_product_categories   = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
		$af_composite_product_tags         = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );

		$products     = array();
		$cats         = array();
		$tags         = array();
		$product_keys = (array) array_keys( $af_composite_product_all_products );
		if ( in_array( $comp_key , $product_keys ) ) {
			$products = (array) $af_composite_product_all_products[ $comp_key ];
		}
		$cat_keys = (array) array_keys( $af_composite_product_categories );
		if ( in_array( $comp_key , $cat_keys ) ) {
			$cats = (array) $af_composite_product_categories[ $comp_key ];
		}
		$tag_keys = (array) array_keys( $af_composite_product_tags );
		if ( in_array( $comp_key , $tag_keys ) ) {
			$tags = (array) $af_composite_product_tags[ $comp_key ];
		}
		$array_products = merge_all_products( $products , $cats , $tags );

		$order_type_arr  = (array) get_post_meta( $product_id , 'af_composite_product_type' , true );
		$order_type_keys = (array) array_keys( $order_type_arr );
		$order_type      = '';
		if ( in_array( $comp_key , $order_type_keys ) ) {
			$order_type = $order_type_arr[ $comp_key ];
		}
		if ( ( 'asc' != $order_type ) && ( 'desc' != $order_type ) ) {
			$order_type = 'asc';
		}

		$order_by_arr  = (array) get_post_meta( $product_id , 'af_composite_product_order' , true );
		$order_by_keys = (array) array_keys( $order_by_arr );
		$order_by      = '';
		if ( in_array( $comp_key , $order_by_keys ) ) {
			$order_by = $order_by_arr[ $comp_key ];
		}
		$args                   = array();
		$args['posts_per_page'] = -1;
		$args['post_type']      = array( 'product', 'product_variation' );
		$args['post__in']       = $array_products;
		$args['post_status']    = 'publish';
		$args['fields']         = 'ids';
		$args['order']          = $order_type;
		
		if ( 'price' == $order_by ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price';
		} elseif ( 'rating' == $order_by ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_wc_average_rating';
		} elseif ( 'name' == $order_by ) {
			$args['orderby'] = 'title';
		} elseif ( 'date' == $order_by ) {
			$args['orderby'] = 'date';
		} elseif ( 'popularity' == $order_by ) {
			$args['orderby'] = 'popularity';
		}

		$return_array = get_posts($args);
		
		return $return_array;
	}
}

if ( !function_exists( 'merge_all_products' ) ) {
	function merge_all_products( $products, $cats, $tags ) {
		$all_products = (array) $products;
		if ( ( is_array($cats) ) && ( !empty($cats) ) ) {
			$all_cats              = array( 
				'numberposts' => -1,    
				'post_status' => array( 'publish' ),
				'post_type'   => array( 'product' ), 
				'fields'      => 'ids',
			);
			$all_cats['tax_query'] = array( 
				array( 
					'taxonomy' => 'product_cat', 
					'field'    => 'id', 
					'terms'    => $cats, 
					'operator' => 'IN', 
				),
			);
			$all_category_products =  get_posts($all_cats);
			$all_products          = array_merge( (array) $all_products , (array) $all_category_products );
		}
		if ( ( is_array($tags) ) && ( !empty($tags) ) ) {
			$all_tags              = array( 
				'numberposts' => -1, 
				'post_status' => array( 'publish' ), 
				'post_type'   => array( 'product' ), 
				'fields'      => 'ids',
			);
			$all_tags['tax_query'] = array(
				array(
					'taxonomy' => 'product_tag',
					'field'    => 'id',
					'terms'    => $tags,
					'operator' => 'IN',
				),
			);
			$all_tag_products      =  get_posts($all_tags);
			$all_products          = array_merge( (array) $all_products , (array) $all_tag_products );
		}
		foreach ( $all_products as $key => $value) {
			if ( !wc_get_product($value) ) {
				unset($all_products[ $key ]);
				continue;
			}
			$product = wc_get_product($value);
			if ( ( !$product->is_type('simple') ) && ( !$product->is_type('variable') ) && ( !$product->is_type('variation') ) ) {
				unset($all_products[ $key ]);
				continue;
			}
			if ( $product ) {
				if ( $product->is_type('variation') ) {
					if ( in_array( $product->get_parent_id() , $all_products ) ) {
						unset($all_products[ $key ]);
						continue;
					}
				}
			} else {
				unset($all_products[ $key ]);
			}
		}
		return $all_products;
	}
}

if ( !function_exists( 'af_cp_component_common_qty' ) ) {
	function af_cp_component_common_qty( $product_id, $qty_option, $comp_key, $fixed_qty ) {


		$af_cp_component_display_type='af_cp_reg_check_' . $qty_option;

		if ( 'fixed' == $qty_option ) {
			?>
					<input type="hidden" name="af_cp_component_product_qty[<?php echo esc_attr($comp_key); ?>]" value="<?php echo esc_attr($fixed_qty); ?>">
				<?php
		} else {

			$min         = get_min_qty_component($product_id, $comp_key);
			$initial_qty = $min;
			$max         = get_max_qty_component($product_id, $comp_key);
			$step        = get_step_qty_component($product_id, $comp_key);
			$add_class_php = 'af_cp_comp_product_comp_quantity_box_' . $comp_key;
		
			
			$af_cp_theme_data           = wp_get_theme();
			$af_cp_theme_name = $af_cp_theme_data->get('Name');


			if ( ( 'Avada'==$af_cp_theme_name )
				 ||( 'Divi'==$af_cp_theme_name )
				 ||( 'Twenty Twenty-Four'==$af_cp_theme_name )
				 ||( 'Storefront'==$af_cp_theme_name )
				 ) {


				if ('Storefront'==$af_cp_theme_name) {
			
			
					?>
			<div class="af_cp_component_inline_center <?php echo esc_attr($add_class_php); ?>" style="width:100%!important;">
		<div class="quantity" style="width:70px!important;margin:0px!important;">
	<input 
		type="number" 
		name="af_cp_component_product_qty[<?php echo esc_attr($comp_key); ?>]" 
		value="<?php echo esc_attr($initial_qty); ?>" 
		min="<?php echo esc_attr($min); ?>" 
		max="<?php echo esc_attr($max); ?>" 
		step="<?php echo esc_attr($step); ?>" 
		class="qty af_cp_component_qty af_cp_select_product_qty" 
		style="font-size: 13px !important;text-align!important:center;padding:0px!important;height:25px!important;width:50px;" 
		>
</div>
<br>
		</div>

			
			<?php
				}       
					
				if ('Twenty Twenty-Four'==$af_cp_theme_name) {
			
			
					?>
				<div class="af_cp_component_inline_center <?php echo esc_attr($add_class_php); ?>" style="width:100%!important;">
			<div class="quantity" style="width:56px!important;margin:0px!important;">
		<input 
			type="number" 
			name="af_cp_component_product_qty[<?php echo esc_attr($comp_key); ?>]" 
			value="<?php echo esc_attr($initial_qty); ?>" 
			min="<?php echo esc_attr($min); ?>" 
			max="<?php echo esc_attr($max); ?>" 
			step="<?php echo esc_attr($step); ?>" 
			class="qty af_cp_component_qty af_cp_select_product_qty" 
			style="font-size: 13px !important;text-align!important:center;padding:0px!important;height:25px!important;width:50px;" 
			>
	</div>
	<br>
			</div>
	
				
				<?php
				}   
								
				if ('Divi'==$af_cp_theme_name) {
			
			
					?>
					<div class="af_cp_component_inline_center <?php echo esc_attr($add_class_php); ?>" style="width:100%!important;">
				<div class="quantity" style="width:56px!important;margin:0px!important;">
			<input 
				type="number" 
				name="af_cp_component_product_qty[<?php echo esc_attr($comp_key); ?>]" 
				value="<?php echo esc_attr($initial_qty); ?>" 
				min="<?php echo esc_attr($min); ?>" 
				max="<?php echo esc_attr($max); ?>" 
				step="<?php echo esc_attr($step); ?>" 
				class="qty af_cp_component_qty af_cp_select_product_qty" 
				style="font-size: 13px !important;text-align!important:center;padding:0px!important;height:25px!important;width:50px;" 
				>
		</div>
		<br>
				</div>
		
					
					<?php
				}   

				if ('Avada'==$af_cp_theme_name) {
			
			
					?>
						<div class="af_cp_component_inline_center <?php echo esc_attr($add_class_php); ?>" style="width:100%!important;max-height:30px!important;">
					<div class="quantity" style="width:60px!important;margin:0px!important;">
				<input 
					type="number" 
					name="af_cp_component_product_qty[<?php echo esc_attr($comp_key); ?>]" 
					value="<?php echo esc_attr($initial_qty); ?>" 
					min="<?php echo esc_attr($min); ?>" 
					max="<?php echo esc_attr($max); ?>" 
					step="<?php echo esc_attr($step); ?>" 
					class="qty af_cp_component_qty af_cp_select_product_qty" 
					style="font-size: 13px !important;text-align!important:center;padding:0px!important;height:21px!important;width:33px;" 
					>
			</div>
			<br>
					</div>	
						<?php
				}
			} else {
				?>
			<div class="af_cp_component_inline_center <?php echo esc_attr($add_class_php); ?>" >
		<div class="quantity" >
		<input 
		type="number" 
		name="af_cp_component_product_qty[<?php echo esc_attr($comp_key); ?>]" 
		value="<?php echo esc_attr($initial_qty); ?>" 
		min="<?php echo esc_attr($min); ?>" 
		max="<?php echo esc_attr($max); ?>" 
		step="<?php echo esc_attr($step); ?>" 
		class="input-text qty text af_cp_component_qty af_cp_select_product_qty"  
		>
</div>
<br>
</div>		
<?php

			}   

					

			
		}
	}
}

if ( !function_exists( 'af_cp_simple_dropdown' ) ) {
	function af_cp_simple_dropdown( $product_id, $comp_key, $default_product, $comp_products, $fixed_qty_text, $fixed_qty, $qty_option ) {
		?>
		<div class="af_cp_select_cover">
			<select name="af_cp_component_product[<?php echo esc_attr($comp_key); ?>]" <?php echo esc_attr( af_cp_required_comp( $product_id , $comp_key ) ); ?> class="af_cp_simple_select2 af_cp_get_simple_dropdown af_cp_select_product af_cp_select_product_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?> af_cp_component_products_fields" data-key="<?php echo esc_attr($comp_key); ?>" data-sel_div="af_cp_selected_product_<?php echo esc_attr($product_id); ?>_<?php echo esc_attr($comp_key); ?>" style="width:100%;">
			<?php
			if ( '' == af_cp_required_comp( $product_id , $comp_key ) ) {
				?>
				   <option value="0"><?php echo esc_html__( 'Select a product' , 'af_comp_product'); ?></option>
				<?php
			}
			if ( !in_array( $default_product , $comp_products ) ) {
				$default_product = current( (array) $comp_products);
			}
			foreach ( $comp_products as $comp_id ) {

				
				$af_cp_product = wc_get_product( $comp_id );

		
				$stock_return_arr = (array) af_cp_stock_in_option( $product_id , $comp_id , $comp_key );
				$disable_op       = false;
				if ( 'disable' == $stock_return_arr['type'] ) {
					$disable_op = true;
				}

				$show_price = af_cp_price_in_option_get_simple( $product_id , $comp_id , $comp_key );
				
				$show_stock = $stock_return_arr['msg'];
				?>
					<option value="<?php echo esc_attr($comp_id); ?>" 
						<?php 
						selected( $default_product , $comp_id );
						if ( $disable_op ) {
							echo ''; } 
						?>
					>
					<?php 
						echo esc_attr( $fixed_qty_text ) . esc_html__( get_the_title($comp_id) , 'af_comp_product'); 
					if ( '' != $show_price ) {
						echo ' &nbsp; ( ' . wp_kses_post( af_cp_price_in_option_get_simple( $product_id , $comp_id , $comp_key ) ) . ' ) ';
					}
					if ( '' != $show_stock ) {
						echo '&nbsp; ( ' . wp_kses_post( $show_stock ) . ' ) ';
					}
					?>
					</option>
				<?php } ?>
			</select>
			<?php if (empty(af_cp_required_comp( $product_id, $comp_key ))) { ?>
				<button style="float:right;" type="button" value="<?php echo esc_attr($comp_key); ?>"  class="simple_clear_all">clear all</button>
			<?php } ?>
		</div>
			<?php
	}
}

if ( !function_exists( 'af_cp_image_dropdown' ) ) {
	function af_cp_image_dropdown( $current_product_id, $comp_key, $default_product, $products, $fixed_qty_text, $fixed_qty, $qty_option ) {
	$template                     = get_post_meta( $current_product_id , 'af_comp_product_layout' , true );

	$af_cp_form_location       = get_post_meta( $current_product_id , 'af_cp_form_location' , true );
	
		?>
			<div class="af-style-drop-down-main af_cp_comp_products_content_<?php echo esc_attr($comp_key); ?> af_cp_select_product_<?php echo esc_attr( $current_product_id . '_' . $comp_key ); ?> " data-key_id="<?php echo esc_attr($comp_key); ?>" >
				<input type="hidden" name="af_cp_component_product[<?php echo esc_attr($comp_key); ?>]" value="<?php echo esc_attr($default_product); ?>" id="select-component-product-<?php echo esc_attr($comp_key); ?>" class="af-form-composite-products af_cp_component_products_fields" data-key_id="<?php echo esc_attr($comp_key); ?>" data-key="<?php echo esc_attr($comp_key); ?>" >
				<div class="af-selected-product-comp af-full-width"  data-key_id="af-cp-dropdown-<?php echo esc_attr($comp_key); ?>">
					<div class="af-cp-image-drpdown af-cp-content-div af-cp-content-div-<?php echo esc_attr($comp_key); ?>"   data-product_id="<?php echo esc_attr($default_product); ?>">
						<?php
						if ( !in_array( $default_product , $products ) ) {
							$default_product = current( (array) $products);
						}
						if ( ( '' != $default_product ) && ( wc_get_product($default_product) ) ) {
							single_component_drop_down( $products , $fixed_qty_text , $current_product_id , $default_product , $comp_key );
						} else {
							$prod_image_id = get_post_meta( $current_product_id , 'af_comp_product_component_image' , true );
							$image         = '';
							if ( isset($prod_image_id[ $comp_key ]) ) {
								$image = current( (array) wp_get_attachment_image_src( (int) $prod_image_id[ $comp_key ] ) );
							}
							if ( '' == $image ) {
								$image = AFCPB_DIR_URL . '/includes/assets/images/image-placeholder.jpg';
							}
							?>
								<img src="<?php echo esc_url( $image ); ?>" >
							<?php
						}
						?>
					</div>
					<div class="af-cp-arrow-div">
						&#9660;
					</div>
				</div>
				<div class="af-comp-products-dropdown af-cp-dropdown-<?php echo esc_attr($comp_key); ?>"  data-selected="af-cp-content-div-<?php echo esc_attr($comp_key); ?>">
					<input type="text" pattern="[A-Za-z]{3}" placeholder="<?php echo esc_html__( 'Enter 3 characters' , 'af_comp_product' ); ?>" class="af-cp-search-img-products af-cp-img-dropdown-ajax-search " data-ajax_search_div=".af-cp-img-dropdown-ajax-search-div-<?php echo esc_attr($comp_key); ?>" data-options="af-single-component-dropdown-<?php echo esc_attr($comp_key); ?>">    
					<?php if ( '' == af_cp_required_comp( $current_product_id , $comp_key ) ) { ?>

						<div style="display: none; font-size:15px; justify-content: flex-start; align-items: center; padding:10px;" class="af-single-component-dropdown   af-single-component-dropdown-<?php echo esc_attr($comp_key); ?>-0  af-full-width" data-input_name="select-component-product-<?php echo esc_attr($comp_key); ?>" data-permalink="#"  data-key_id="<?php echo esc_attr($comp_key); ?>"  data-product_id="0">
							<h4 class="title" style="margin-left:15px; font-size:15px; margin-top:25px;"><?php echo esc_html__( 'Choose a product' , 'af_comp_product' ); ?></h4>
						</div>

					<?php } ?>
					<div class="af-cp-img-dropdown-ajax-search-div-<?php echo esc_attr($comp_key); ?>" ></div>
					<?php

					foreach ($products as $key => $product_id ) {
						$product = wc_get_product($product_id);

				

			

						
						?>
						<div class="af-single-component-dropdown af-single-component-dropdown-<?php echo esc_attr($comp_key); ?>  af-full-width 
						<?php 
						if ( $product_id == $default_product ) {
							echo 'af-selected-product-option'; } 
						?>
						" data-permalink="<?php echo esc_url($product->get_permalink()); ?>" data-input_name="select-component-product-<?php echo esc_attr($comp_key); ?>" data-name="<?php echo esc_html__( get_the_title($product_id) , 'af_comp_product' ); ?>" data-key_id="<?php echo esc_attr($comp_key); ?>"  data-product_id="<?php echo esc_attr($product_id); ?>">
						<?php 
						single_component_drop_down( $products , $fixed_qty_text , $current_product_id , $product_id , $comp_key );
						?>
						</div>
						<?php
					}
					?>
				</div>
				<?php if (empty(af_cp_required_comp( $current_product_id, $comp_key ))) { ?>
				<button style="float:right;" type="button" value="<?php echo esc_attr($comp_key); ?>"  class="clear_all">clear all</button>
				<?php } ?>
			</div>
			
		<?php
	}
}

if ( !function_exists( 'af_cp_radio_options' ) ) {
	function af_cp_radio_options( $current_product_id, $comp_key, $default_product, $products, $fixed_qty_text, $fixed_qty, $qty_option ) {
			
		
	
		
		$af_cp_show_cross_or_tick = 'cross';
		$af_cp_req_show='radio_comp_not_requrired_' . $comp_key;
		if (!empty(af_cp_required_comp( $current_product_id, $comp_key ))) {
			$af_cp_show_cross_or_tick = 'tick';
			$af_cp_req_show='radio_comp_requrired_' . $comp_key;
		}
	
		?>
				<div class="af_cp_comp_radio_op_div <?php echo esc_attr($af_cp_req_show); ?> af_cp_select_product_<?php echo esc_attr( $current_product_id . '_' . $comp_key ); ?> ">
				<?php

				if ( !in_array( $default_product , $products ) ) {
					$default_product = current( (array) $products);
				}

				if ( '' == af_cp_required_comp( $current_product_id , $comp_key ) ) {

					?>
						<div class="af_cp_single_radio  af_cp_radio_optional_div">
							<div class="af_cp_single_radio-btn">
								<input type="radio" name="af_cp_component_product[<?php echo esc_attr($comp_key); ?>]" value="0" class="af_cp_comp_radio_options af_cp_component_products_fields" id="af_cp_radio_op_<?php echo esc_attr($current_product_id) . '_' . esc_attr($comp_key); ?>" data-key="<?php echo esc_attr($comp_key); ?>" <?php checked( 0 , $default_product ); ?>>
							</div>
							<div class="af_cp_comp_radio_div">
								<?php 
									single_component_drop_down( $products , $fixed_qty_text , $current_product_id , 0 , $comp_key );
								?>
							</div>
						</div>
					<?php
				}

				foreach ( $products as $single_product_id ) {

					$btn_text_class = '';
					if ( $default_product == $single_product_id ) {
						$btn_text_class = 'af_cp_radiowise_selected';
					}

							
				$af_cp_product = wc_get_product( $single_product_id );

			



				$af_cp_create_product_class = 'af_cp_radio_product_type_' . $af_cp_product->get_type();

					?>
						<div class="af_cp_single_radio <?php echo esc_attr( ( $single_product_id == $default_product ) ? ' af_cp_selected_radio_product_item ' : ''); ?> ">
						<?php
						if ( 'af_cp_radiowise_selected' == $btn_text_class ) {
							if ('cross'==$af_cp_show_cross_or_tick) {
								?>
								<span class="radio-close-circle">x</span>
								<?php
						
							} elseif ('tick'==$af_cp_show_cross_or_tick) {
								?>
								<span class="radio-open-circle">✓</span>
								<?php
							}
							?>
						<?php } ?>	
						<div class="af_cp_single_radio-btn">
								<input type="radio" name="af_cp_component_product[<?php echo esc_attr($comp_key); ?>]" value="<?php echo esc_attr($single_product_id); ?>" class="af_cp_comp_radio_options af_cp_component_products_fields <?php echo esc_attr($af_cp_create_product_class); ?>" id="af_cp_radio_op_<?php echo esc_attr($current_product_id) . '_' . esc_attr($comp_key); ?>" data-key="<?php echo esc_attr($comp_key); ?>" <?php checked( $single_product_id , $default_product ); ?>>
							</div>
							<div class="af_cp_comp_radio_div">
						<?php 
							single_component_drop_down( $products , $fixed_qty_text , $current_product_id , $single_product_id , $comp_key );
						?>
					
							</div>
						</div>
					<?php
				}
				?>
				</div>
			<?php
			af_cp_component_common_qty( $current_product_id, $qty_option, $comp_key, $fixed_qty );
	}
}

if ( !function_exists( 'af_cp_thumbnail_options' ) ) {
	function af_cp_thumbnail_options( $current_product_id, $comp_key, $default_product, $all_products, $fixed_qty_text, $fixed_qty, $qty_option, $next_page ) {
		$args = array();
		$args['posts_per_page'] = 12;
		$args['post_type'] = array( 'product', 'product_variation' );
		$args['post__in'] = $all_products;
		$args['post_status'] = 'publish';
		$args['fields'] = 'ids';
		$args['paged'] = $next_page;
		$products = get_posts( $args );

		// Ensure $default_product is included and appears first
		if ( !empty( $default_product ) && in_array( $default_product, $all_products ) ) {
			$products = array_diff( $products, array( $default_product ) ); // Remove if already in the array
			array_unshift( $products, $default_product ); // Add to the beginning
		}

		
		$af_cp_show_cross_or_tick = 'cross';
		$af_cp_req_show='thumbnail_comp_not_requrired_' . $comp_key;
		if (!empty(af_cp_required_comp( $current_product_id, $comp_key ))) {
			$af_cp_show_cross_or_tick = 'tick';
			$af_cp_req_show='thumbnail_comp_requrired_' . $comp_key;
		}
		$af_cp_form_location       = get_post_meta( $current_product_id , 'af_cp_form_location' , true );
	
		?>

		<div class="af_cp_thumbnail_main  <?php echo esc_attr($af_cp_req_show); ?>  af_cp_select_product_<?php echo esc_attr( $current_product_id . '_' . $comp_key ); ?> ">
			<?php
			if ( '' == af_cp_required_comp( $current_product_id, $comp_key ) ) {
				$prod_image_id = get_post_meta( $current_product_id, 'af_comp_product_component_image', true );
				$image = '';
				if ( isset( $prod_image_id[ $comp_key ] ) ) {
					$image = current( (array) wp_get_attachment_image_src( (int) $prod_image_id[ $comp_key ] ) );
				}
				if ( '' == $image ) {
					$image = AFCPB_DIR_URL . '/includes/assets/images/image-placeholder.jpg';
				}
				$btn_text = 'Select';
				$btn_text_class = '';
				if ( '0' == $default_product ) {
					$btn_text = 'Selected';
					$btn_text_class = 'af_cp_thumbnail_selected';
				} elseif ( !in_array( $default_product, (array) $products ) ) {
					$default_product = current( (array) $products );
				}
				?>
				<div class="af_cp_single_prod_th ">
					<div class="image">
						<img src="<?php echo esc_url( $image ); ?>" >
					</div>
					<div class="desc">
						<?php echo '<p class="title">' . esc_attr( $fixed_qty_text ) . esc_html__( 'Select a product', 'af_comp_product' ) . '</p>'; ?>
					</div>
					<div class="select_btn">
						<input type="hidden" data-product_id="0" class="af_cp_select_component_thumb <?php echo esc_attr( $btn_text_class ); ?>" value="<?php echo esc_html__( $btn_text, 'af_comp_product' ); ?>" data-select="<?php echo esc_html__( 'Select', 'af_comp_product' ); ?>" data-selected="<?php echo esc_html__( 'Selected', 'af_comp_product' ); ?>" data-input_name="select-component-product-<?php echo esc_attr( $comp_key ); ?>" >
					</div>
				</div>
				<?php
			} elseif ( !in_array( $default_product, (array) $products ) ) {
				$default_product = current( (array) $products );
			}

			foreach ( $products as $product_id ) {
				
				
				$af_cp_product = wc_get_product( $product_id );

				


				$af_cp_create_product_class = 'af_cp_thumbnail_product_type_' . $af_cp_product->get_type();
				

				$image = current( (array) wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' ) );
				if ( '' == $image ) {
					$prod_image_id = get_post_meta( $current_product_id, 'af_comp_product_component_image', true );
					if ( isset( $prod_image_id[ $comp_key ] ) ) {
						$image = current( (array) wp_get_attachment_image_src( (int) $prod_image_id[ $comp_key ] ) );
					}
				}
				if ( '' == $image ) {
					$image = AFCPB_DIR_URL . '/includes/assets/images/image-placeholder.jpg';
				}
				$stock_return_arr = (array) af_cp_stock_in_option( $current_product_id, $product_id, $comp_key );
				$show_price = af_cp_price_in_option( $current_product_id, $product_id, $comp_key );
				$show_stock = $stock_return_arr['msg'];
				$btn_text = 'Select';
				$btn_text_class = '';
				if ( $default_product == $product_id ) {
					$btn_text = 'Selected';
					$btn_text_class = 'af_cp_thumbnail_selected';
				}
				?>
				<div class="af_cp_single_prod_th <?php echo esc_attr( ( 'af_cp_thumbnail_selected' == $btn_text_class ) ? ' af_cp_selected_product_item ' : '' ); ?> ">
					<div class="image">
						<img src="<?php echo esc_url( $image ); ?>" >
						<?php
						if ( 'af_cp_thumbnail_selected' == $btn_text_class ) {
							if ('cross'==$af_cp_show_cross_or_tick) {
								?>
								<span class="close-circle">x</span>
								<?php
						
							} elseif ('tick'==$af_cp_show_cross_or_tick) {
								?>
								<span class="open-circle">✓</span>
								<?php
							}
							?>
						<?php } ?>
					</div>
					<div class="desc">
						<?php
						echo '<p class="title">' . esc_html( get_the_title( $product_id ) ) . '</p>';
						if ( '' != $show_price ) {
							echo '<p>' . wp_kses_post( $show_price ) . '</p>';
						}

						if ( '' != $show_stock ) {
							echo '<p>' . wp_kses_post( $show_stock ) . '</p>';
						}
						?>
					</div>
					<div class="select_btn">
						<input type="hidden" data-product_id="<?php echo esc_attr( $product_id ); ?>" class="af_cp_select_component_thumb <?php echo esc_attr( $af_cp_create_product_class ); ?> <?php echo esc_attr( $btn_text_class ); ?>" value="<?php echo esc_html__( $btn_text, 'af_comp_product' ); ?>" data-select="<?php echo esc_html__( 'Select', 'af_comp_product' ); ?>" data-selected="<?php echo esc_html__( 'Selected', 'af_comp_product' ); ?>" data-input_name="select-component-product-<?php echo esc_attr( $comp_key ); ?>" >
					</div>
					<div class="view_product_comp desc" style="width:100%!important;" >
						
					<?php
					if (!empty(get_option( 'wc_settings_tab_composite_product_af_cp_view_product' ))) {
						?>
					   <a  href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" style="font-size:12px;margin-top:3px;display:inline-block;" class="view_product" target="_blank">
								  <?php echo esc_attr(get_option( 'wc_settings_tab_composite_product_af_cp_view_product' )); ?>
					   </a>
							<?php } ?>
					</div>
				</div>
				<?php
			}
			?>
			<div class="af_cp_pagination_div" data-product_id="<?php echo esc_attr( $current_product_id ); ?>" data-comp_key="<?php echo esc_attr( $comp_key ); ?>">
				<?php
				$count_pages = ceil( count( $all_products ) / 9 );
				echo wp_kses_post(
					paginate_links(
						array(
							'current' => max( $next_page, get_query_var( 'paged' ) ),
							'total' => (int) $count_pages,
						)
					)
				);
				?>
			</div>
			<input type="hidden" name="af_cp_component_product[<?php echo esc_attr( $comp_key ); ?>]" value="<?php echo esc_attr( $default_product ); ?>" id="select-component-product-<?php echo esc_attr( $comp_key ); ?>" class="af-form-composite-products af_cp_component_products_fields" data-key="<?php echo esc_attr( $comp_key ); ?>" data-key_id="<?php echo esc_attr( $comp_key ); ?>"  >
		</div>
		<?php
		af_cp_component_common_qty( $current_product_id, $qty_option, $comp_key, $fixed_qty );
	}
}



if ( !function_exists( 'single_component_drop_down' ) ) {
	function single_component_drop_down( $products, $fixed_qty_text, $current_product_id, $product_id, $comp_key ) {
		$image = '';
		if ( 0 == $product_id ) {
			$prod_image_id = get_post_meta( $current_product_id , 'af_comp_product_component_image' , true );
			$image         = '';
			if ( isset($prod_image_id[ $comp_key ]) ) {
				$image = current( (array) wp_get_attachment_image_src( (int) $prod_image_id[ $comp_key ] ) );
			}
			if ( '' == $image ) {
				$image = AFCPB_DIR_URL . '/includes/assets/images/image-placeholder.jpg';
			}
			?>
				
			<div class="af-cp-image-drpdown">
				<img src="<?php echo esc_url( $image ); ?>" alt="">
				<?php echo '<p class="title">' . esc_html__( 'Select a Product' , 'af_comp_product' ) . '</p>'; ?>
			</div>
			<?php
		} else {
			if ( !in_array( $product_id , (array) $products ) ) {
				$product_id = current( (array) $products);
			}
			if ( is_array(wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' )) ) {
				$image = current( (array) wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' ) );
			}
			$prod_image_id = get_post_meta( $current_product_id , 'af_comp_product_component_image' , true );
			if ( '' == $image ) {
				if ( isset($prod_image_id[ $comp_key ]) ) {
					$image = current( (array) wp_get_attachment_image_src( (int) $prod_image_id[ $comp_key ] ) );
				}
			}
			if ( '' == $image ) {
				$image = AFCPB_DIR_URL . '/includes/assets/images/image-placeholder.jpg';
			}
			$stock_return_arr = (array) af_cp_stock_in_option( $current_product_id , $product_id , $comp_key );
			$show_price       = af_cp_price_in_option( $current_product_id , $product_id , $comp_key );
			$show_stock       = $stock_return_arr['msg'];
			?>
			<div class="af-cp-dropdown-with-pro-img">
		
				<img src="<?php echo esc_url( $image ); ?>" alt="">
				<div>
					<?php
					echo '<p class="title">' . esc_attr($fixed_qty_text) . esc_html__( get_the_title($product_id) , 'af_comp_product' ) . '</p>';
					if ( '' != $show_price ) {
						echo '<span>' . wp_kses_post( $show_price ) . '</span>';
						 echo '<span style="margin-right:10px"></span>';
					}
					if ( '' != $show_stock ) {
						echo '<span class="dropdown-with-pro-img-stock">' . wp_kses_post( $show_stock ) . '</span>';
					}
					?>
					<div class="view_product_comp desc" style="display:flex;width:100%!important;">
			<?php
			if (!empty(get_option( 'wc_settings_tab_composite_product_af_cp_view_product' ))) {
				?>
					<a  href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" style="font-size:12px;margin-top:3px;" class="view_product" target="_blank">
						  <?php echo esc_attr(get_option( 'wc_settings_tab_composite_product_af_cp_view_product' )); ?>
					</a>
					<?php } ?>
					</div>
				</div>
			</div>
			<?php
		}
	}
}

if ( !function_exists( 'af_cp_validate_scenario_fn' ) ) {
	function af_cp_validate_scenario_fn( $product_id, $form_table ) {
		$enable_scenario_cbs = (array) get_post_meta( $product_id , 'af_cp_enable_scenario_cb' , true );
		$scenario_comp       = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_comp' , true );
		$scenario_type       = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_type' , true );
		$scenario_products   = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_products' , true );
			
		$hide_comp_cb    = (array) get_post_meta( $product_id , 'af_comp_product_sc_hide_comp_cb' , true );
		$hide_options_cb = (array) get_post_meta( $product_id , 'af_comp_product_sc_hide_options_cb' , true );
			
		$action_comp         = (array) get_post_meta( $product_id , 'af_cp_scenario_action_comp' , true );
		$action_type         = (array) get_post_meta( $product_id , 'af_cp_scenario_action_type' , true );
		$action_products     = (array) get_post_meta( $product_id , 'af_cp_scenario_action_products' , true );
		$return_hidden_class = array();

		$components_in_form = array();
		if ( isset( $form_table['af_cp_component_product'] ) ) {
			$components_in_form = (array) $form_table['af_cp_component_product'];
		
		}

		$all_comp_products = array();
		foreach ( $components_in_form as $comp_key => $value ) {
			$all_comp_products[ $comp_key ] = (array) af_cp_component_products( $product_id , $comp_key );
		}
		$comp_all_keys = (array) array_keys($components_in_form);
		$first_comp    = current($comp_all_keys);
		foreach ( $enable_scenario_cbs as $scenario_key => $cb_value ) {
			if ( 'yes' == $cb_value ) {
				if ( isset($scenario_comp[ $scenario_key ]) ) {
					$current_products = array();
					if ( isset($scenario_products[ $scenario_key ]) ) {
						$current_products = (array) $scenario_products[ $scenario_key ];
					}
					$current_sce_type_arr = array();
					if ( isset($scenario_type[ $scenario_key ]) ) {
						$current_sce_type_arr = (array) $scenario_type[ $scenario_key ];
					}

					$scenario_components = (array) $scenario_comp[ $scenario_key ];
					$check_conditions    = true;
					$cond_count          = 0;
					$cond_comp_arr       = array();
					foreach ( $scenario_components as $condition_key => $comp_key ) {
						$local_check        = false;
						$curr_comp_products = array();
						$cur_prod_sel_comp  = 0;
						if ( isset($components_in_form[ $comp_key ]) ) {
							$cur_prod_sel_comp = $components_in_form[ $comp_key ];
							if ( isset($all_comp_products[ $comp_key ]) ) {
								$curr_comp_products = $all_comp_products[ $comp_key ];
							}
						}
						if ( isset($current_products[ $condition_key ]) ) {
							$current_scenario_products = (array) $current_products[ $condition_key ];
							$current_sce_type          = '';
							if ( isset($current_sce_type_arr[ $condition_key ]) ) {
								$current_sce_type = $current_sce_type_arr[ $condition_key ];
							}
							++$cond_count;
							$cond_comp_arr[] = $comp_key;
							if ( 'is' == $current_sce_type ) {
								if ( in_array( $cur_prod_sel_comp , $current_scenario_products ) ) {
									$local_check = true;
								} else {
									$local_check = false;
								}
							} elseif ( 'not' == $current_sce_type ) {
								if ( !in_array( $cur_prod_sel_comp , $current_scenario_products ) ) {
									$local_check = true;
								} else {
									$local_check = false;
								}
							} elseif ( 'any' == $current_sce_type ) {
								$local_check = true;
							}
							if ( $local_check ) {
								if ( $check_conditions ) {
									$check_conditions = $local_check;
								}
							} else {
								$check_conditions = $local_check;
							}
						}
					}
					// previously added cond_count
					if ( $check_conditions ) {
						$comp_products       = array();
						$action_comp_arr     = (array) get_post_meta( $product_id , 'af_cp_scenario_action_comp' , true );
						$action_type_arr     = (array) get_post_meta( $product_id , 'af_cp_scenario_action_type' , true );
						$action_products_arr = (array) get_post_meta( $product_id , 'af_cp_scenario_action_products' , true );
			
						
						if ( isset($hide_options_cb[ $scenario_key ]) ) {
							if ( 'yes' == $hide_options_cb[ $scenario_key ] ) {
								if ( isset($action_comp_arr[ $scenario_key ]) ) {
									$action_type = '';
									if ( isset($action_type_arr[ $scenario_key ]) ) {
										$action_type = $action_type_arr[ $scenario_key ];
									}
									$action_products = array();
									if ( isset($action_products_arr[ $scenario_key ]) ) {
										$action_products = (array) $action_products_arr[ $scenario_key ];
									}
									$action_comp = $action_comp_arr[ $scenario_key ];

							
									foreach ( $action_comp as $action_key => $action_value ) {
										if ( in_array( $action_value , $cond_comp_arr ) ) {
											continue;
										}
										if ( $first_comp == $action_value ) {
											continue;
										}
										if ( isset( $action_type[ $action_key ] ) ) {
											$action_type_selection = $action_type[ $action_key ];
											$action_sel_products   = array();
											if ( isset( $action_products[ $action_key ] ) ) {
												$action_sel_products = $action_products[ $action_key ];
											}
											if ( 'hide' == $action_type_selection ) {
												if ( isset($all_comp_products[ $action_value ]) ) {
													$all_comp_products[ $action_value ] = array_diff( $all_comp_products[ $action_value ], $action_sel_products );
												}
											} elseif ( 'hide_except' == $action_type_selection ) {
												if ( !empty($action_sel_products) ) {
													$all_comp_products[ $action_value ] = $action_sel_products;
												}
											}
										}
									}
								}
							}
						}
						if ( isset($hide_comp_cb[ $scenario_key ]) ) {
							if ( 'yes' == $hide_comp_cb[ $scenario_key ] ) {
								$hide_comps_arr = (array) get_post_meta( $product_id , 'af_cp_sc_hide_comps' , true );
								if ( isset($hide_comps_arr[ $scenario_key ]) ) {
									$af_cp_sc_hide_comps = $hide_comps_arr[ $scenario_key ];
									
									
									foreach ( $af_cp_sc_hide_comps as $hide_comp_key => $hide_comp_value ) {
										if ( in_array( $hide_comp_value , $cond_comp_arr ) ) {
											continue;
										}

										// if ( $first_comp == $hide_comp_value ) {
										//  continue;
										// }
									
										$return_hidden_class[] = $hide_comp_value;
									}
								}
							}
						}
					}
				}
			}
		}

		$return_array = array(
			'options' => (array) $all_comp_products,
			'hide'    => (array) $return_hidden_class,
		);

		return $return_array;
	}
}

if ( !function_exists( 'af_cp_selected_condition' ) ) {
	function af_cp_selected_condition( $meta, $key, $condition_key, $choice ) {
		if ( array_key_exists( $key , $meta ) ) {
			$meta_key = (array) $meta[ $key ];
			if ( array_key_exists( $condition_key , $meta_key ) ) {
				if ( $choice == $meta_key[ $condition_key ] ) {
					return 'selected';
				}
			}
		}
		return '';
	}
}

if ( !function_exists( 'af_cp_selected_scenario_actions' ) ) {
	function af_cp_selected_scenario_actions( $meta, $key, $condition_key, $choice ) {
		if ( array_key_exists( $key , $meta ) ) {
			$meta_key = (array) $meta[ $key ];
			if ( array_key_exists( $condition_key , $meta_key ) ) {
				if ( $choice == $meta_key[ $condition_key ] ) {
					return 'selected';
				}
			}
		}
		return '';
	}
}

if ( !function_exists( 'af_cp_child_comp_qty_array' ) ) {
	function af_cp_child_comp_qty_array( $product_id, $comp_key ) {
		$af_cp_quantity_op = (array) get_post_meta( $product_id , 'af_composite_product_quantity_op' , true );
		$qty_option        = '';
		$fixed_qty_text    = '';
		$fixed_qty         = 1;

		$min_qty  = 1;
		$max_qty  = -1;
		$step_qty = 1;
		$type     = 'range';

		if ( isset($af_cp_quantity_op[ $comp_key ]) ) {
			$qty_option = $af_cp_quantity_op[ $comp_key ];
		}
		$return_array = array(
			'type' => 'range',
			'min'  => 1,
			'max'  => -1,
			'step' => 1,
		);
		if ( 'fixed' == $qty_option ) {
			$fixed_qty_op = (array) get_post_meta( $product_id , 'af_composite_product_fixed_qty' , true );
			if ( isset($fixed_qty_op[ $comp_key ]) ) {
				$fixed_qty = $fixed_qty_op[ $comp_key ];
			}
			if ( '' == $fixed_qty ) {
				$fixed_qty = 1;
			}
			$return_array = array(
				'type' => 'fixed',
				'min'  => $fixed_qty,
				'max'  => $fixed_qty,
				'step' => 1,
			);
		} else {
			$min_qty  = get_min_qty_component( $product_id , $comp_key );
			$max_qty  = get_max_qty_component( $product_id , $comp_key );
			$step_qty = get_step_qty_component( $product_id , $comp_key );
			if ( '' != $max_qty ) {
				if ( $min_qty >= $max_qty ) {
					$type = 'fixed';
				}
			}
			$return_array = array(
				'type' => $type,
				'min'  => $min_qty,
				'max'  => $max_qty,
				'step' => $step_qty,
			);
		}
		return $return_array;
	}
}

if ( !function_exists( 'af_cp_price_in_option_get_simple' ) ) {
	function af_cp_price_in_option_get_simple( $current_product_id, $product_id, $comp_key ) {
		$product = wc_get_product( $product_id );
	
		$af_cp_parent_product_price_type=get_post_meta( $current_product_id , 'af_comp_product_price_type' , true );
		
		
		if ( '' == $product->get_price() ) {
			return '';
		}

		if ('variable'==$product->get_type()) {
			
			$show_var_prices = $product->get_variation_prices( true );
			$var_min_price = $show_var_prices['price'] ? min( $show_var_prices['price'] ) : 0;
			$var_max_price = $show_var_prices['price'] ? max( $show_var_prices['price'] ) : 0;
	
			$var_min_price_html = wc_price( $var_min_price );
			$var_max_price_html = wc_price( $var_max_price );

			return '<span class="show_template_product_range_' . $comp_key . '">' . $var_min_price_html . ' - ' . $var_max_price_html . '</span>';
	
		}

		$price_op_arr  = (array) get_post_meta( $current_product_id , 'af_composite_product_price_style' , true );
		$price_op_keys = (array) array_keys( $price_op_arr );
		$price_msg     = '';
		if ( in_array( $comp_key , $price_op_keys ) ) {
			$price_op = $price_op_arr[ $comp_key ];
			if ( ( 'active' == $price_op )||( 'full_price' == $price_op ) ) {
				
				if ('fixed_price'==$af_cp_parent_product_price_type) {
					
					$price_msg = wc_price(0);

				} elseif ('calculated_price'==$af_cp_parent_product_price_type) {

					$price_msg = wc_price( af_cp_component_product_price( $current_product_id , $product_id , $comp_key , $product->get_price() ));
					
				}
				
			} 
		}




		return $price_msg;
	}
}

if ( !function_exists( 'addify_composite_product_action_buttons' ) ) {
	function addify_composite_product_action_buttons( $product ) {
		$product_id = $product->get_id();
		if (!af_cp_check_rfq_is_active_and_compatible_version()) {
			?>
			<button type="submit" name="add-to-cart"  value="<?php echo esc_attr($product_id); ?>" data-href="?add-to-cart=<?php echo esc_attr($product_id); ?>" id="afcp-add-to-cart-product" class="af_cp_cart_btn single_add_to_cart_button button alt wp-block-button__link" disabled><?php echo esc_html__( $product->add_to_cart_text() , 'af_comp_product' ); ?></button>

			<?php
			return;
			
		} else {
			af_cp_check_rfq_rule_logic($product);
			return;
		}
		?>

		<?php
	}
}


if ( !function_exists( 'af_cp_check_rfq_is_active_and_compatible_version' ) ) {
	function af_cp_check_rfq_is_active_and_compatible_version() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_path = 'woocommerce-request-a-quote/class-addify-request-for-quote.php';
		$required_version = '2.8.0';

		if ( is_plugin_active( $plugin_path ) ) {
			$plugins = get_plugins();

			if ( isset( $plugins[ $plugin_path ] ) ) {
				$current_version = $plugins[ $plugin_path ]['Version'];

				if ( version_compare( $current_version, $required_version, '>=' ) ) {
					return true;
				}
			}
		}
		return false;
	}
}

if (!function_exists('af_cp_check_rfq_rule_logic')) {
	function af_cp_check_rfq_rule_logic( $product ) {

		$args = array(
			'post_type'        => 'addify_rfq',
			'post_status'      => 'publish',
			'numberposts'      => -1,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => false,

		);
		$quote_rules = get_posts( $args );


		foreach ( $quote_rules as $rule ) {

			$afrfq_is_hide_price      = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
			$afrfq_hide_price_text    = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );
			$afrfq_is_hide_addtocart  = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
			$afrfq_custom_button_text = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );
			$afrfq_custom_button_link = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_link', true );

			$afrfq_apply_on_oos_products = get_post_meta( intval( $rule->ID ), 'afrfq_apply_on_oos_products', true );

			$istrue = false;

			if ( $product->is_in_stock() ) {

				if ( 'yes' == $afrfq_apply_on_oos_products ) {
					continue;
				}
			}

			if ( !af_cp_check_afrfq_rule( $product->get_id(), $rule->ID ) ) {
				continue;
			}


			if ('replace' == $afrfq_is_hide_addtocart) {
				echo '<a href="javascript:void(0)" rel="nofollow" data-rule_id="' . esc_attr($rule->ID) . '" data-product_id="' . intval( $product->get_ID() ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" class="afrfqbt_single_page wp-element-button button single_add_to_cart_button alt product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
				do_action( 'addify_after_add_to_quote_button' );
				return;
			} else if ('addnewbutton' == $afrfq_is_hide_addtocart) {
				echo '<button type="submit" name="add-to-cart"  value="' . esc_attr($product->get_id()) . '" data-href="?add-to-cart=' . esc_attr($product->get_id()) . '" id="afcp-add-to-cart-product" class="af_cp_cart_btn single_add_to_cart_button button alt wp-block-button__link" disabled>' . esc_html__( $product->add_to_cart_text() , 'af_comp_product' ) . '</button>';
				echo '<a href="javascript:void(0)" rel="nofollow" data-rule_id="' . esc_attr($rule->ID) . '" data-product_id="' . intval( $product->get_ID() ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" class="afrfqbt_single_page  single_add_to_cart_button button alt product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
				do_action( 'addify_after_add_to_quote_button' );
				return;
			} else if ('replace_custom' == $afrfq_is_hide_addtocart) {
				if ( ! empty( $afrfq_custom_button_text ) ) {
					echo '<a href="' . esc_url( $afrfq_custom_button_link ) . '" rel="nofollow" class="button afcp-custom-button afcp-button-link product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
				}
				do_action( 'addify_after_add_to_quote_button' );
				return;
			} else if ('addnewbutton_custom' == $afrfq_is_hide_addtocart) {
				echo '<button type="submit" name="add-to-cart"  value="' . esc_attr($product->get_id()) . '" data-href="?add-to-cart=' . esc_attr($product->get_id()) . '" id="afcp-add-to-cart-product" class="af_cp_cart_btn single_add_to_cart_button button alt wp-block-button__link" disabled>' . esc_html__( $product->add_to_cart_text() , 'af_comp_product' ) . '</button>';
				if ( ! empty( $afrfq_custom_button_text ) ) {
					echo '<a href="' . esc_url( $afrfq_custom_button_link ) . '" rel="nofollow" class="button afcp-custom-button afcp-button-link product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
				}
				do_action( 'addify_after_add_to_quote_button' );
				return;

			}
		}

		echo '<button type="submit" name="add-to-cart"  value="' . esc_attr($product->get_id()) . '" data-href="?add-to-cart=' . esc_attr($product->get_id()) . '" id="afcp-add-to-cart-product" class="af_cp_cart_btn single_add_to_cart_button button alt wp-block-button__link" disabled>' . esc_html__( $product->add_to_cart_text() , 'af_comp_product' ) . '</button>';
		return;
	}
}


if (!function_exists('af_cp_check_afrfq_rule')) { 
	function af_cp_check_afrfq_rule( $product_id, $rule_id ) {
		$afrfq_rule_type       = get_post_meta( intval( $rule_id ), 'afrfq_rule_type', true );
		$afrfq_hide_products   = (array) unserialize( get_post_meta( intval( $rule_id ), 'afrfq_hide_products', true ) );
		$afrfq_hide_categories = (array) unserialize( get_post_meta( intval( $rule_id ), 'afrfq_hide_categories', true ) );
		$afrfq_hide_brands     = (array) unserialize( get_post_meta( intval( $rule_id ), 'afrfq_hide_brands', true ) );
		$afrfq_hide_user_role  = (array) unserialize( get_post_meta( intval( $rule_id ), 'afrfq_hide_user_role', true ) );

		$afrfq_apply_all_user_role = get_post_meta( intval( $rule_id ), 'afrfq_apply_on_all_user_role', true );
		$applied_on_all_products   = get_post_meta( $rule_id, 'afrfq_apply_on_all_products', true );


		if ( ! is_user_logged_in() ) {

			if ( ( ! in_array( 'guest', (array) $afrfq_hide_user_role, true ) && 'afrfq_for_guest_users' !== $afrfq_rule_type ) && ( 'yes' != $afrfq_apply_all_user_role ) ) {

				return false;
			}
		} else {

			$curr_user      = wp_get_current_user();
			$curr_user_role = current( $curr_user->roles );

			if ( ( ! in_array( $curr_user_role, (array) $afrfq_hide_user_role, true ) ) && ( 'yes' != $afrfq_apply_all_user_role ) ) {
				return false;
			}
		}

		if ( 'yes' === $applied_on_all_products ) {
			return true;
		}

		if ( in_array( $product_id, $afrfq_hide_products ) ) {
			return true;
		}

		foreach ( $afrfq_hide_categories as $cat ) {

			if ( ! empty( $cat ) && has_term( $cat, 'product_cat', $product_id ) ) {

				return true;
			}
		}

		foreach ( $afrfq_hide_brands as $brand ) {

			if ( ! empty( $brand ) && has_term( $brand, 'product_brand', $product_id ) ) {

				return true;
			}
		}

		return false;
	}
}






