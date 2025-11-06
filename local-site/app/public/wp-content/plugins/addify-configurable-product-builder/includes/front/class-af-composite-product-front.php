<?php


defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'ADF_Composite_Product_Front' ) ) {
	
	class ADF_Composite_Product_Front {

		public function __construct() {
			
			add_action('wp_enqueue_scripts', array( $this, 'afcpb_front_scripts' ) , 90);

			add_filter('woocommerce_loop_add_to_cart_link', array( $this, 'afcpb_shop_page_button' ), 10, 2); 

			add_action('woocommerce_product_meta_start', array( $this, 'afcpb_before_product_add_to_cart' ), 60 );

			add_action('woocommerce_after_single_product_summary', array( $this, 'afcpb_after_composite_product_summary' ), 10 );

			add_filter('woocommerce_add_to_cart_validation', array( $this, 'afcpb_add_to_Cart_Validation' ), 10, 5);

			add_filter('woocommerce_update_cart_validation', array( $this, 'afcpb_qty_update_cart_validation' ), 1, 4);

			add_filter('woocommerce_add_cart_item_data', array( $this, 'afcpb_add_cart_item_data_fn_cb' ), 10, 3);
			
			add_action('woocommerce_add_to_cart', array( $this, 'afcpb_add_to_cart_fn_cb' ), 90 , 6 );

			add_action('woocommerce_before_calculate_totals', array( $this, 'afcpb_add_component_products' ), 10 , 1);

			add_filter('woocommerce_get_cart_item_from_session', array( $this, 'afcpb_get_cart_item_from_session' ), 20, 2 );

			add_filter('woocommerce_cart_item_price', array( $this, 'afcpb_show_price_cart_basket' ), 10 , 3 );
				
			add_filter('woocommerce_cart_item_remove_link', array( $this, 'afcpb_restrict_user_to_remove_item' ) , 20, 2 );
			
			add_filter('woocommerce_cart_item_subtotal', array( $this, 'afcpb_cart_product_subtotal' ), 80, 3 );
			
			add_filter('woocommerce_quantity_input_args', array( $this, 'afcpb_validation_min_max_product_quantity' ), 99, 2 );

			add_filter('woocommerce_cart_item_quantity', array( $this, 'afcpb_disable_quantity_in_cart' ), 10, 2);

			add_action('woocommerce_new_order', array( $this, 'afcpb_remove_html_content_and_tags_on_place_order' ), 10, 1);

			add_action('woocommerce_remove_cart_item', array( $this, 'afcpb_remove_cart_item' ), 10, 2 );

			add_filter('woocommerce_cart_item_name', array( $this, 'afcpb_cart_product_name' ), 10, 3 );

			add_filter('woocommerce_order_item_name', array( $this, 'afcpb_order_product_name' ), 10, 2 );

			add_filter('woocommerce_order_formatted_line_subtotal', array( $this, 'afcpb_order_formatted_line_subtotal_filter' ), 10, 3 );

			add_action('woocommerce_checkout_create_order_line_item', array( $this, 'afcpb_update_order_item_meta' ), 20, 4 );
			
			add_filter('woocommerce_store_api_product_quantity_minimum', array( $this, 'afcpb_api_product_quantity_minimum' ), 10, 3 );
		   
			add_filter('woocommerce_store_api_product_quantity_maximum', array( $this, 'afcpb_api_product_quantity_maximum' ), 10, 3 );
			
			add_filter('woocommerce_store_api_product_quantity_multiple_of', array( $this, 'afcpb_api_product_quantity_multiple_of' ), 10, 3 );

			add_filter('woocommerce_store_api_product_quantity_editable', array( $this, 'afcpb_api_product_quantity_editable' ), 10, 3 );

			add_filter('woocommerce_package_rates', array( $this, 'set_shipping_fee_zero_for_specific_cart_item' ), 10, 2);

			add_action('wp_head', array( $this, 'afcpb_theme_dependent_styling' ));
		}   

		
		public function afcpb_api_product_quantity_editable( $value, $product, $cart_item ) {
		
			if (!empty($cart_item['data'])) {

				$product_object = $cart_item['data'];

				if (!empty($product_object->get_type())) {


					$af_cp_get_parent_by_id_step=$cart_item['data']->get_id();
					$af_cp_get_parent_by_check_step_quantity = get_post_meta( $af_cp_get_parent_by_id_step , 'af_comp_product_adj_step_qty' , true );
					if (!empty($af_cp_get_parent_by_check_step_quantity)) {
						
						if ($af_cp_get_parent_by_check_step_quantity>1) {
							$get_composite_name='<p class="show_component_hide_block"><b>hide_composite</b></p>' . $cart_item['data']->get_name();
							$cart_item['data']->set_name($get_composite_name); 
						}
					}

					if (( !empty($cart_item['composite_child_products']['type']) )&&( 'component'==( $cart_item['composite_child_products']['type'] ) )) {
						
						$af_cb_parent_product_id = (int) $cart_item['composite_child_products']['parent_product_id'];

						$af_cp_allow_edit_in_cart = get_post_meta( $af_cb_parent_product_id , 'af_cp_allow_edit_in_cart' , true );

						$af_cp_check_step_quantity = get_post_meta( $af_cb_parent_product_id , 'af_comp_product_adj_step_qty' , true );
						
						$af_cb_composite_comp_key = (int) $cart_item['composite_child_products']['comp_key'];

						$component_quantity_type =  get_post_meta( $af_cb_parent_product_id , 'af_composite_product_quantity_op' , true );

						$comp_names = (array) get_post_meta( $af_cb_parent_product_id , 'af_comp_product_component_name' , true );
				
						if (!empty($comp_names[ $af_cb_composite_comp_key ])) {
				
							$name = $comp_names[ $af_cb_composite_comp_key ];
							
							if ( $cart_item['data']->is_type( 'variation' ) ) {
								if (strpos($cart_item['data']->get_description(), '_af_cp_block_before_variation_') === false) {

									$get_componet_dsp = $name . ' _af_cp_block_before_variation_ ' . $cart_item['data']->get_description();

									$cart_item['data']->set_description($get_componet_dsp);

								}
								
							} else if (strpos($cart_item['data']->get_name(), '<span class="show_component_name_block">') === false) {

									$get_componet_name = '<span class="show_component_name_block"><b>' . $name . '</b></span>' . $cart_item['data']->get_name();

									$cart_item['data']->set_name($get_componet_name);
								
							}

						}
												

						
						if (( empty($af_cp_allow_edit_in_cart) )||( 'yes'!=$af_cp_allow_edit_in_cart )) {                   

							if ( $cart_item['data']->is_type( 'variation' ) ) {
								// Check and append description for variation type
								if (strpos($cart_item['data']->get_description(), '_af_cp_block_hide_variation_') === false) {
									$get_component_desc = ' _af_cp_block_hide_variation_ ' . $cart_item['data']->get_description();
									$cart_item['data']->set_description($get_component_desc);
								}
							} else if (strpos($cart_item['data']->get_name(), '<p class="show_component_hide_block">') === false) {
									$get_component_name = '<p class="show_component_hide_block"><b>hide_composite</b></p>' . $cart_item['data']->get_name();
									$cart_item['data']->set_name($get_component_name);
							}
							
							
						}

						if (( !empty($component_quantity_type[ $af_cb_composite_comp_key ]) )&&( 'fixed'==( $component_quantity_type[ $af_cb_composite_comp_key ] ) )) {
							if ( $cart_item['data']->is_type( 'variation' ) ) {
								// Check and append description for variation type
								if (strpos($cart_item['data']->get_description(), '_af_cp_block_hide_variation_') === false) {
									$get_component_desc = ' _af_cp_block_hide_variation_ ' . $cart_item['data']->get_description();
									$cart_item['data']->set_description($get_component_desc);
								}
							} else if (strpos($cart_item['data']->get_name(), '<p class="show_component_hide_block">') === false) {
									$get_component_name = '<p class="show_component_hide_block"><b>hide_composite</b></p>' . $cart_item['data']->get_name();
									$cart_item['data']->set_name($get_component_name);
							}
							
							
						}
						if (!empty($af_cp_check_step_quantity)) {
							if ($af_cp_check_step_quantity>1) {
								if ( $cart_item['data']->is_type( 'variation' ) ) {
									// Check and append description for variation type
									if (strpos($cart_item['data']->get_description(), '_af_cp_block_hide_variation_') === false) {
										$get_component_desc = ' _af_cp_block_hide_variation_ ' . $cart_item['data']->get_description();
										$cart_item['data']->set_description($get_component_desc);
									}
								} else if (strpos($cart_item['data']->get_name(), '<p class="show_component_hide_block">') === false) {
										$get_component_name = '<p class="show_component_hide_block"><b>hide_composite</b></p>' . $cart_item['data']->get_name();
										$cart_item['data']->set_name($get_component_name);
								}
								
								
							}
						}
					
					}
	
				}

			}
			return $value;
		}


		public function afcpb_api_product_quantity_minimum( $value, $product, $cart_item ) {
	
			if (!empty($cart_item['data'])) {
				$product_object = $cart_item['data'];
				
				if (!empty($product_object->get_type())) {
					
					if ($product_object->get_type()=='af_composite_product') {
				
						$afcb_configurable_product_id =(int) $product_object->get_id();
						
						$configure_product_min   = (int) get_post_meta( $afcb_configurable_product_id , 'af_comp_product_adj_min_qty' , true );
						
						if (!empty($configure_product_min)) {
							return $configure_product_min;
						}
												
				
					} else if (( !empty($cart_item['composite_child_products']['type']) )&&( 'component'==( $cart_item['composite_child_products']['type'] ) )) {


						$af_cb_set_min_qty_for_component=0;

						$afcb_parent_product_quantity=(int) $cart_item['composite_child_products']['parent_quantity'];
						

						$afcb_component_min = (int) $cart_item['composite_child_products']['min'];
					
						$afcb_component_product_id = (int) $product_object->get_id();
						
						$af_cb_parent_product_id = (int) $cart_item['composite_child_products']['parent_product_id'];

						$af_cb_composite_comp_key = (int) $cart_item['composite_child_products']['comp_key'];

						$component_quantity_type =  get_post_meta( $af_cb_parent_product_id , 'af_composite_product_quantity_op' , true );

						if (!empty($afcb_component_min)) {
							
							$af_cb_set_min_qty_for_component = ( $afcb_component_min * $afcb_parent_product_quantity );

							if (( !empty($component_quantity_type[ $af_cb_composite_comp_key ]) )&&( 'range'==( $component_quantity_type[ $af_cb_composite_comp_key ] ) )) {
								return $af_cb_set_min_qty_for_component;
							}
						}
					
					}
	
				}

			}

			return $value;
		}
		
	
		public function afcpb_api_product_quantity_maximum( $value, $product, $cart_item ) {
	
			if (!empty($cart_item['data'])) {
				$product_object = $cart_item['data'];
				
				if (!empty($product_object->get_type())) {
					
					if ($product_object->get_type()=='af_composite_product') {
				
						$afcb_configurable_product_id =(int) $product_object->get_id();

						$configure_product_min = (int) get_post_meta( $afcb_configurable_product_id , 'af_comp_product_adj_max_qty' , true );
						
						if (!empty($configure_product_min)) {
							return $configure_product_min;
						}
				
					} else if (( !empty($cart_item['composite_child_products']['type']) )&&( 'component'==( $cart_item['composite_child_products']['type'] ) )) {

						$af_cb_set_min_qty_for_component=0;

						$afcb_parent_product_quantity=(int) $cart_item['composite_child_products']['parent_quantity'];

						$afcb_component_min = (int) $cart_item['composite_child_products']['max'];
					
						$afcb_component_product_id = (int) $product_object->get_id();
						
						$af_cb_parent_product_id = (int) $cart_item['composite_child_products']['parent_product_id'];

						$af_cb_composite_comp_key = (int) $cart_item['composite_child_products']['comp_key'];

						$component_quantity_type =  get_post_meta( $af_cb_parent_product_id , 'af_composite_product_quantity_op' , true );

						if (!empty($afcb_component_min)) {

						   $af_cb_set_min_qty_for_component = ( $afcb_component_min * $afcb_parent_product_quantity );
							
							if (( !empty($component_quantity_type[ $af_cb_composite_comp_key ]) )&&( 'range'==( $component_quantity_type[ $af_cb_composite_comp_key ] ) )) {
							 return $af_cb_set_min_qty_for_component;
							}
						}
					
					}
	
				}

			}

			return $value;
		}  


		public function afcpb_api_product_quantity_multiple_of( $value, $product, $cart_item ) {
			
			if (!empty($cart_item['data'])) {
				$product_object = $cart_item['data'];
				
				if (( !empty($cart_item['composite_child_products']['type']) )&&( 'component'==( $cart_item['composite_child_products']['type'] ) )) {

						$af_cb_set_min_qty_for_component=0;

						$afcb_parent_product_quantity=(int) $cart_item['composite_child_products']['parent_quantity'];

						$afcb_component_min = (int) $cart_item['composite_child_products']['max'];
					
						$afcb_component_product_id = (int) $product_object->get_id();
						
						$af_cb_parent_product_id = (int) $cart_item['composite_child_products']['parent_product_id'];

						$af_cb_composite_comp_key = (int) $cart_item['composite_child_products']['comp_key'];

						$component_quantity_type =  get_post_meta( $af_cb_parent_product_id , 'af_composite_product_quantity_op' , true );

					if (!empty($afcb_parent_product_quantity)) {
						
						if ($afcb_parent_product_quantity>1) {
								
							if (( !empty($component_quantity_type[ $af_cb_composite_comp_key ]) )&&( 'range'==( $component_quantity_type[ $af_cb_composite_comp_key ] ) )) {
								return $afcb_parent_product_quantity;
							}

						}
					}
				}
	
				

			}
		



			return $value;
		}  

		public function afcpb_front_scripts() {

			$arr_compsite_products_incart = array();

			if ( WC()->cart && ! WC()->cart->is_empty() ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$product = $cart_item['data'];

			
					if ( $product && $product->get_type() == 'af_composite_product' ) {
						$configure_product_id = $product->get_id();
						$product_name = $product->get_name();
						$product_type = $product->get_type();

						$configure_product_min   = get_post_meta( $configure_product_id , 'af_comp_product_adj_min_qty' , true );
						$configure_product_max   = get_post_meta( $configure_product_id , 'af_comp_product_adj_max_qty' , true );
						$configure_product_steps = get_post_meta( $configure_product_id , 'af_comp_product_adj_step_qty' , true );

						$arr_compsite_products_incart[] = array(
							'product_id'   => $configure_product_id,
							'product_name' => $product_name,
							'product_type' => $product_type,
							'product_min'  => $configure_product_min,
							'product_max'  => $configure_product_max,
							'product_steps'=> $configure_product_steps,
						);
					}
				}
			}
	

			wp_enqueue_style('af-composite-product-front-css', plugins_url('../assets/css/af-comp-product-front.css', __FILE__), false, '1.0.0');
			wp_enqueue_script('jquery');

			wp_enqueue_script('af-composite-product-front-script', plugins_url('../assets/js/af-comp-product-front-script.js', __FILE__), false, '1.1.0', $in_footer = false);

			$afcpb_ajax_data = array(
				'admin_url' => admin_url('admin-ajax.php'),
				'nonce'     => wp_create_nonce('afcpb_files_nonce'),
				'af_comp_all_produts_in_cart'=>$arr_compsite_products_incart,
			
			);

			wp_localize_script(
				'af-composite-product-front-script',
				'af_comp_product',
				$afcpb_ajax_data
			);
			wp_enqueue_script( 'wc-add-to-cart-variation' );

			wp_enqueue_style( 'select2-css', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ) , array(), '5.7.2' );

			wp_enqueue_script('select2-js', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );
		}
	   

		public function afcpb_shop_page_button( $html, $product ) {

			$current_theme = wp_get_theme();
			$parent_theme  = $current_theme->parent();

			if ($current_theme->get('Name') === 'Twenty Twenty-Five' || ( $parent_theme && $parent_theme->get('Name') === 'Twenty Twenty-Five' )) { 

				if ( $product->is_type('af_composite_product') ) {
					return '
							<div data-block-name="woocommerce/product-button" data-font-size="small" data-is-descendent-of-query-loop="true" data-is-inherited="1" data-style="{&quot;spacing&quot;:{&quot;margin&quot;:{&quot;bottom&quot;:&quot;1rem&quot;}}}" data-text-align="center" class="wp-block-button wc-block-components-product-button   align-center wp-block-woocommerce-product-button has-small-font-size">
								<a href="' . $product->get_permalink() . '" class="button wp-element-button wc-block-components-product-button__button add_to_cart_button ajax_add_to_cart product_type_simple has-font-size has-small-font-size has-text-align-center wc-interactive wp-block-button__link">' . esc_html__( 'Select Options' , 'af_comp_product' ) . '</a>
							</div>';
	
				}

			} elseif ( $product->is_type('af_composite_product') ) {
					return '<a href="' . $product->get_permalink() . '" class="button wp-element-button wc-block-components-product-button__button add_to_cart_button ajax_add_to_cart product_type_simple has-font-size has-small-font-size has-text-align-center wc-interactive wp-block-button__link">' . esc_html__( 'Select Options' , 'af_comp_product' ) . '</a>';
			}


			return $html;
		}

		public function afcpb_after_composite_product_summary() {
			global $product;
			$product_id          = $product->get_id();
			$af_cp_form_location = get_post_meta( $product_id , 'af_cp_form_location' , true );

			if ( 'after_sum' != $af_cp_form_location ) {
				return;
			}
			?>
			<div class="f_cp_after_summary">
				<?php
					$this->af_cp_content( $product_id );
				?>
			</div>
			<?php
		}
		
		public function afcpb_before_product_add_to_cart() {
			global $product;
			$product_id          = $product->get_id();
			$af_cp_form_location = get_post_meta( $product_id , 'af_cp_form_location' , true );

			if ( 'after_sum' == $af_cp_form_location ) {
				return;
			}

			?>
			<div class="f_cp_after_desc">
				<?php
					$this->af_cp_content( $product_id );
				?>
			</div>
			<?php
		}

		public function afcpb_remove_html_content_and_tags_on_place_order( $order_id ) {

			$order = wc_get_order($order_id);
		
			foreach ($order->get_items() as $item_id => $item) {
				// Get the product name
				$product_name = $item->get_name();
		
				// Remove the content inside the HTML tags first
				$cleaned_name = preg_replace('/>.*?</', '><', $product_name); // This removes the inner content between tags
				
				// Now remove the tags themselves
				$cleaned_name = strip_tags($cleaned_name);
		
				// Trim any whitespace that might be left
				$cleaned_name = trim($cleaned_name);
		
				// Set the cleaned product name
				$item->set_name($cleaned_name);
		
				// Save the updated item
				$item->save();
			}
		}

		public function af_cp_content( $product_id ) {
			global $product;
			if ( '' == $product->get_price() ) {
				return;
			}
			$product_id = $product->get_id();

			if ( 'af_composite_product' != $product->get_type() ) {
				return;
			}

			if ( ! $product->is_in_stock() ) {
				echo wp_kses_post( wc_get_stock_html( $product ) ); // phpcs:ignore WordPress.Security.EscapeOutput.
				return;
			}

			if ( !$this->afcpb_check_product_have_component( $product_id ) ) {
				return;
			}

			$af_cp_form_location = get_post_meta( $product_id , 'af_cp_form_location' , true );
			$af_cp_comp_name     = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
			$all_comp_products   = array();

			foreach ( $af_cp_comp_name  as $comp_key => $comp_name ) {
				$all_comp_products[ $comp_key ] = (array) af_cp_component_products( $product_id , $comp_key );
			}

			$general_desc = get_post_meta( $product_id , 'af_comp_product_text' , true );
			?>
			<form action="" class="cart af_cp_cart_form" method="post">
				<?php wp_nonce_field( 'afcpb_files_nonce_action', 'afcpb_files_nonce' ); ?>
				<p><?php echo wp_kses_post( $general_desc ); ?></p>
				<input type="hidden" class="af_cp_current_product_id" value="<?php echo esc_attr($product_id); ?>" >
				<div class="af_cp_all_components_content">
					<?php include AFCPB_DIR_PATH . '/templates/single-product/af-component-templates.php'; ?>
				</div>
				&nbsp;&nbsp;
				<?php woocommerce_quantity_input( array(), $product, true ); ?>
				<?php addify_composite_product_action_buttons($product); ?>
				<!-- <button type="submit" name="add-to-cart"  value="<?php echo esc_attr($product_id); ?>" data-href="?add-to-cart=<?php echo esc_attr($product_id); ?>" id="afcp-add-to-cart-product" class="af_cp_cart_btn single_add_to_cart_button button alt" disabled><?php echo esc_html__( $product->add_to_cart_text() , 'af_comp_product' ); ?></button> -->
				<!-- <button type="submit" name="add-to-cart"  value="<?php echo esc_attr($product_id); ?>" data-href="?add-to-cart=<?php echo esc_attr($product_id); ?>" id="afcp-add-to-cart-product" class="af_cp_cart_btn single_add_to_cart_button button alt" disabled><?php echo esc_html__( 'This will be quote button' , 'af_comp_product' ); ?></button> -->
			</form>
			<?php
		}

		public function afcpb_check_product_have_component( $product_id ) {
			$component_name = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );

			if ( empty($component_name) ) {
				return false;
			}

			$check_if_empty_products = false;

			foreach ( $component_name as $key => $value) {

				$selected_products   = array();
				$selected_categories = array();
				$selected_tags       = array();
				$products            = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );
				$categories          = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
				$tags                = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );
				$products_keys       = (array) array_keys( $products );

				if ( in_array( $key , $products_keys ) ) {
					$selected_products = $products[ $key ];
				}

				$categories_keys = (array) array_keys( $categories );
				if ( in_array( $key , $categories_keys ) ) {
					$selected_categories = $categories[ $key ];
				}

				$tags_keys = (array) array_keys( $tags );
				if ( in_array( $key , $tags_keys ) ) {
					$selected_tags = $tags[ $key ];
				}

				$all_products = (array) merge_all_products( $selected_products , $selected_categories , $selected_tags );
				if ( !empty($all_products) ) {
					$check_if_empty_products = true;
					break;
				}

			}
			return $check_if_empty_products;
		}

		public function af_cp_sorting_div( $product_id, $comp_key ) {
			?>
				<div class="af_cp_sort_products" data-id="<?php echo esc_attr($product_id); ?>" data-key="<?php echo esc_attr($comp_key ); ?>" >
					<select class="af_cp_sort_type" data-class_attr="single_products_<?php echo esc_attr($product_id . '_' . $comp_key ); ?>" data-key="<?php echo esc_attr($comp_key ); ?>" >
						<option value="default"><?php echo esc_html__('Default' , 'af_comp_product'); ?></option>
						<option value="price"><?php echo esc_html__('Price' , 'af_comp_product'); ?></option>
						<option value="name"><?php echo esc_html__('Name' , 'af_comp_product'); ?></option>
						<option value="date"><?php echo esc_html__('Date' , 'af_comp_product'); ?></option>
						<option value="rating"><?php echo esc_html__('Rating' , 'af_comp_product'); ?></option>
						<option value="popularity"><?php echo esc_html__('Popularity' , 'af_comp_product'); ?></option>
					</select>
					<select class="af_cp_sort_order" data-class_attr="single_products_<?php echo esc_attr($product_id . '_' . $comp_key ); ?>" data-key="<?php echo esc_attr($comp_key ); ?>" >
						<option value="default"><?php echo esc_html__('Default' , 'af_comp_product'); ?></option>
						<option value="asc"><?php echo esc_html__('Ascending' , 'af_comp_product'); ?></option>
						<option value="desc"><?php echo esc_html__('Descending' , 'af_comp_product'); ?></option>
					</select>
				</div>
			<?php
		}

		public function afcpb_add_to_Cart_Validation( $passed, $product_id, $quantity, $variation_id = '', $variations = '' ) {
			$product = wc_get_product($product_id);

			if ( 'af_composite_product' != $product->get_type() ) {
				return $passed;
			}

			$af_comp_product_adj_min_qty = get_post_meta( $product_id , 'af_comp_product_adj_min_qty' , true );
			$af_comp_product_adj_max_qty = get_post_meta( $product_id , 'af_comp_product_adj_max_qty' , true );
			$quantity                    = sanitize_text_field( isset($_REQUEST['quantity']) ? $_REQUEST['quantity'] : 1 );
			
			if ( ( '' != $af_comp_product_adj_max_qty ) && ( '' != $af_comp_product_adj_max_qty )  ) {

				if ( ( $quantity < $af_comp_product_adj_min_qty ) || ( $quantity > $af_comp_product_adj_max_qty ) ) {

					wc_add_notice( __( ' Quantity of ' . get_the_title($product_id) . ' must be between ' . $af_comp_product_adj_min_qty . ' and ' . $af_comp_product_adj_max_qty , 'woocommerce'), 'error');

					return false;

				}
			} elseif ( ( '' != $af_comp_product_adj_max_qty ) && ( '' == $af_comp_product_adj_max_qty ) ) {
				
				if ( $quantity < $af_comp_product_adj_min_qty ) {
					wc_add_notice( __( ' Quantity of ' . get_the_title($product_id) . ' must be greater than ' . $af_comp_product_adj_min_qty , 'woocommerce'), 'error');
					return false;
				}

			} elseif ( ( '' == $af_comp_product_adj_max_qty ) && ( '' != $af_comp_product_adj_max_qty )  ) {

				if ( $quantity > $af_comp_product_adj_max_qty ) {
					wc_add_notice( __( ' Quantity of ' . get_the_title($product_id) . ' must be lass than ' . $af_comp_product_adj_max_qty , 'woocommerce'), 'error');
					return false;
				}

			}

			$cart_object    = wc()->cart->get_cart();
			$qty_calculated = 0;

			foreach ( $cart_object as $cart_valid_value ) {

				if ( $cart_valid_value['data']->get_id() == $product_id ) {
					$qty_calculated += $cart_valid_value['quantity'];
				}

			}
			// change_it
			if ( '' != $af_comp_product_adj_max_qty ) {

				if ( $qty_calculated > $af_comp_product_adj_max_qty ) {
					wc_add_notice( __( get_the_title($product_id) . ' is Already in cart More can not be added '  , 'woocommerce'), 'error');
					return false;
				}

				if ( ( $qty_calculated + $quantity )> $af_comp_product_adj_max_qty  ) {
					$remaining_text = '';

					if ( ( $af_comp_product_adj_max_qty - $qty_calculated ) > 0 ) {
						$remaining_text = ' only ' . ( (int) $af_comp_product_adj_max_qty - (int) $qty_calculated ) . ' more can be added';
					}

					wc_add_notice( __( $qty_calculated . ' ' . get_the_title($product_id) . ' are already in Cart ' . $remaining_text , 'woocommerce'), 'error');
					return false;
				}
			}
			$all_component_products = array();
			
			if ( isset($_REQUEST['af_cp_component_product']) ) {
				$all_component_products = (array) sanitize_meta('', $_REQUEST['af_cp_component_product'] , '');
			}
			$all_cp_variation_ids = array();

			if ( isset($_REQUEST['af_cp_variation_id']) ) {
				$all_cp_variation_ids = (array) sanitize_meta('', $_REQUEST['af_cp_variation_id'] , '' );
			}
			$all_qty = array();

			if ( isset($_REQUEST['af_cp_component_product_qty']) ) {
				$all_qty = (array) sanitize_meta('', $_REQUEST['af_cp_component_product_qty'] , '' );
			}

			$scenario_validation     = (array) af_cp_validate_scenario_fn( $product_id , $_REQUEST );
			$scenario_filer_products = (array) $scenario_validation['options'];
			$scenario_hidden_comp    = (array) $scenario_validation['hide'];

			foreach ( $all_component_products as $key => $value) {

				if ( in_array( $key , $scenario_hidden_comp ) ) {
					unset($all_component_products[ $key ]);
				}

				if ( wc_get_product($value) ) {
					
					if ( isset( $scenario_filer_products[ $key ] ) ) {

						if ( !in_array( $value , (array) $scenario_filer_products[ $key ] ) ) {

							wc_add_notice( __( get_the_title($value) . ' cannot be selected ' , 'woocommerce'), 'error');
							$passed = false;
						}
					}
				}
			}
			if ( !$passed ) {
				return $passed;
			}

			foreach ( $all_component_products as $key => $prod_id ) {
				$exclusive_comp      = get_post_meta( $product_id , 'af_composite_product_exclusive_cb' , true );
				$exclusive_component = '';

				if ( array_key_exists( $key , (array) $exclusive_comp ) ) {
					$exclusive_component = $exclusive_comp[ $key ];
				}

				if ( 'yes' == $exclusive_component ) {
					$exclusive_products_msgs[ $key ] = $prod_id;
				}

			}
			
			$cart                    = wc()->cart->get_cart();
			$exclusive_products_msgs = array();
			$comp_msg_added          = array();

			foreach ( $all_component_products as $comp_key => $prod_id ) {

				if ( !wc_get_product($prod_id) ) {
					continue;
				}

				if ( wc_get_product($prod_id)->is_type('variable') ) {
					$all_cp_variation_keys = (array) array_keys($all_cp_variation_ids);

					if ( in_array( $comp_key , $all_cp_variation_keys ) ) {
						$prod_id = $all_cp_variation_ids[ $comp_key ];
					}
				}

				foreach ( $exclusive_products_msgs as $exc_key => $exc_prod_id ) {

					if ( ( $exc_key != $comp_key ) && ( $prod_id == $exc_prod_id ) ) {
						$allow_cart = 'no';

						if ( in_array( $exc_prod_id , (array) $comp_msg_added ) ) {
							continue;
						}

						$comp_msg_added[ $comp_key ] = $prod_id;
						$component_name              = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
						$component_title             = '';

						if ( array_key_exists( $exc_key , $component_name ) ) {
							$component_title = ' "' . $component_name[ $exc_key ] . '" ';
						}

						wc_add_notice( __( get_the_title($prod_id) . ' is selected in exclusive component ' . $component_title . ' . Please choose another product' , 'woocommerce'), 'error');
						$passed = false;
					}
				}

				if ( $passed ) {
					$prod_quantity = 1;

					if ( isset($all_qty[ $comp_key ]) ) {
						$prod_quantity = floatval( $all_qty[ $comp_key ]);
					}
					
					if ( 0 == $prod_quantity ) {
						$prod_quantity = get_min_qty_component( $product_id , $comp_key );
					}

					if ( wc_get_product($prod_id) ) {

						foreach ( $cart as $cart_value ) {

							if ( $prod_id == $cart_value['data']->get_id() ) {
								$prod_quantity += $cart_value['quantity'];
							}
						}

						if ('af_composite_product' != $product->get_type()) {
							$total_qty          = $prod_quantity * $quantity;
						} else {
							$total_qty          = $quantity;
						}

						
						$product_have_stock = (array) af_cp_product_stock( $product_id );
						if ( 'yes' != $product_have_stock['type'] ) {

							if ( '' == $product_have_stock['count'] ) {

								wc_add_notice(__( get_the_title($product_id) . ' is out of stock' , 'woocommerce'), 'error');
								$passed = false;
							} elseif ( $total_qty > $product_have_stock['count'] ) {

								wc_add_notice(__( get_the_title($product_id) . ' have stock of (' . $product_have_stock['count'] . ') more can not selected' , 'woocommerce'), 'error');
								$passed = false;
							}
						}
					}
				}
			}
			return $passed;
		}

		public function afcpb_qty_update_cart_validation( $passed, $cart_item_key, $values, $quantity ) {
			$cart       = WC()->cart->get_cart();
			$product_id = $values['data']->get_id();
			$product    = wc_get_product($product_id);

			if ( ( 'af_composite_product' != $product->get_type() ) || ( !array_key_exists( 'composite_child_products' , $values ) ) ) {
				return $passed;
			}
			$all_component_products = array();

			if ( array_key_exists( 'af_cp_component_product' , $values ) ) {
				$all_component_products = $values['af_cp_component_product'];
			}
			$all_qty = array();

			if ( array_key_exists( 'af_cp_component_product_qty' , $values ) ) {
				$all_qty = $values['af_cp_component_product_qty'];
			}

			foreach ( $all_component_products as $comp_key => $prod_id ) {
				$prod_quantity = $all_qty[ $comp_key ];

				if ( wc_get_product($prod_id) ) {

					foreach ( $cart as $cart_value ) {

						if ( $prod_id == $cart_value['data']->get_id() ) {
							$prod_quantity += $cart_value['quantity'];
						}
					}
					$total_qty          = $prod_quantity * $quantity;
					$product_have_stock = (array) af_cp_product_stock( $prod_id );

					if ( 'yes' != $product_have_stock['type'] ) {

						if ( '' == $product_have_stock['count'] ) {

							wc_add_notice(__( get_the_title($prod_id) . ' is out of stock' , 'woocommerce'), 'error');
							$passed = false;
						} elseif ( $total_qty > $product_have_stock['count'] ) {

							wc_add_notice(__( get_the_title($prod_id) . ' have stock of (' . $product_have_stock['count'] . ') more can not selected' , 'woocommerce'), 'error');
							$passed = false;
						}
					}
				}
			}
			return $passed;
		}

		public function check_stock_in_cart_qty_by_product_id( $product_id, $quantity ) {
			$product = wc_get_product($product_id);

			if ( 'parent' === $product->managing_stock() ) {
				$product = wc_get_product($product->get_parent_id());
			}
			$stock =  $product->get_stock_quantity();

			if ( ( '' == $product->managing_stock() ) && ( ( 'instock' == $product->get_stock_status() ) || ( 'onbackorder' == $product->get_stock_status() ) ) ) {
					return false;
			}

			if ( ( 'instock' != $product->get_stock_status() ) && ( 'onbackorder' != $product->get_stock_status() ) ) {
				return true;
			}
			
			if ( ( 1 == $product->managing_stock() ) ) {
				$back_order_op = get_post_meta( $product->get_id(), '_backorders', true );

				if ( 'no' == $back_order_op ) {
					if (( '' == $stock ) || ( !$stock ) ) {
						// empty stock
						return true;
					}

					if ($quantity > $stock) {
						// less stock
						return true;
					}
				}
			}
			return false;
		}

		public function afcpb_add_to_cart_fn_cb( $cart_item_key ) {
			$this->afcpb_add_component_products( wc()->cart );
		}
		
		public function afcpb_add_component_products( $cart_object ) {
			$cart = $cart_object->get_cart();

			foreach ($cart as $cart_value ) {

				if ( isset($cart_value['composite_child_products']) ) {
					$find_parent  = $cart_value['composite_child_products']['parent_id'];
					$check_parent = true;

					foreach ( $cart as $remove_child_value ) {

						if ( $find_parent == $remove_child_value['key'] ) {
							$check_parent = false;
						}
					}

					if ( $check_parent ) {
						WC()->cart->remove_cart_item($cart_value['key']);
						continue;
					}
				}

				if ( ( array_key_exists( 'af_cp_component_product' , $cart_value ) ) && ( $cart_value['data']->is_type('af_composite_product') ) ) {
					$all_components = (array) $cart_value['af_cp_component_product'];
					$all_qty        = array();

					if ( array_key_exists( 'af_cp_component_product_qty' , $cart_value ) ) {
						$all_qty = (array) $cart_value['af_cp_component_product_qty'];
					}

					$variation_keys = array();
					if ( isset($cart_value['af_cp_variation_id']) ) {
						$variation_keys = $cart_value['af_cp_variation_id'];
					}
					
					$scenario_validate       = (array) af_cp_validate_scenario_fn( $cart_value['data']->get_id() , $cart_value );
					$return_hidden_class_arr = (array) $scenario_validate['hide'];

					foreach ($all_components as $key => $comp_product_id ) {

						if ( in_array( $key , $return_hidden_class_arr ) ) {
							continue;
						}
						$product = wc_get_product($comp_product_id);

						if ( !$product ) {
							continue;
						}
						$qty = 1;

						if ( array_key_exists( $key , $all_qty ) ) {
							$qty = floatval( $all_qty[ $key ]);
							if ( 0 == $qty ) {
								$qty = get_min_qty_component( $cart_value['data']->get_id() , $key );
							}
						}

						$qty_max = 1;   
						$qty_max = get_max_qty_component( $cart_value['data']->get_id() , $key );

						$parent_identity = array( 
							'composite_child_products' => array(
								'parent_id'         => $cart_value['key'],
								'parent_quantity'   => $cart_value['quantity'],
								'parent_product_id' => $cart_value['data']->get_id(),
								'type'              => 'component',
								'qty'               => $qty,
								'min'               => $qty,
								'max'               => floatval($qty_max),
								'comp_key'          => $key,
								'price_type'        => 'component',
							),
						);
						$variation_id    = 0;
					
			


						if ( isset($variation_keys[ $key ]) ) {
							$variation_id = $variation_keys[ $key ];
						}

						$variation_attr = array();
						
						if ( $product->is_type('variable') ) {
							$attributes = $product->get_variation_attributes();
							foreach ( $attributes as $child_key => $child_value ) {
								$child_attr_key = strtolower('attribute_' . $child_key);
								if ( isset($cart_value[ $child_attr_key ]) ) {
									$variation_attr[ $child_attr_key ] =  $cart_value[ $child_attr_key ][ $key ];
								}
							}
						}

				
						if ( $this->check_if_component_is_already_added( $cart_value['key'] , $comp_product_id , $key , $variation_id , $variation_attr ) ) {

							remove_action('woocommerce_before_calculate_totals', array( $this, 'afcpb_add_component_products' ), 10, 1);

							WC()->cart->add_to_cart($comp_product_id , ( $qty * $cart_value['quantity'] ) , $variation_id, $variation_attr  , $parent_identity );

							add_action('woocommerce_before_calculate_totals', array( $this, 'afcpb_add_component_products' ), 10, 1);

						}
					}

				}
				
				if ( $cart_value['data']->is_type('af_composite_product') ) {
					$cart_value['data']->set_price( $this->composite_product_price_calculate( $cart_value['data']->get_id() , $cart_value['key'] ) );
				}

				if ( array_key_exists( 'composite_child_products' , $cart_value ) ) {
					$cart_value['data']->set_price(0);

					if ( isset($cart[ $cart_value['composite_child_products']['parent_id'] ]) ) {
						$parent_quantity   = $cart[ $cart_value['composite_child_products']['parent_id'] ]['quantity'];
						$own_quantity      = $cart_value['quantity'];
						$child_array       = $cart_value['composite_child_products'];
						$child_parent_qty  = $child_array['parent_quantity'];
						$old_child_own_qty = $child_array['qty'];
						$child_own_qty     = $child_array['qty'];
						global $woocommerce;
						$check_qty_change = false;

						if ( ( $child_parent_qty != $parent_quantity ) && ( $child_own_qty != $own_quantity ) ) {

							$check_qty_change = true;
							$child_own_qty    = $own_quantity / $child_parent_qty;
						} elseif ( ( $child_parent_qty != $parent_quantity ) && ( $child_own_qty == $own_quantity ) ) {

							$check_qty_change = true;
							$child_own_qty    = $own_quantity / $child_parent_qty;

						} elseif ( ( $child_parent_qty == $parent_quantity ) && ( $child_own_qty != $own_quantity ) ) {
							$check_qty_change = true;
							$child_own_qty    = $own_quantity / $child_parent_qty;

						}
						$child_own_qty   = ceil($child_own_qty);
						$update_quantity = ceil( $child_own_qty *  $parent_quantity );
						
						if ( $check_qty_change ) {

							remove_action('woocommerce_before_calculate_totals', array( $this, 'afcpb_add_component_products' ), 10, 1);

							$cart_object->set_quantity( $cart_value['key'] , $update_quantity , true );
							$woocommerce->cart->cart_contents[ $cart_value['key'] ]['composite_child_products']['qty']             = $child_own_qty;
							$woocommerce->cart->cart_contents[ $cart_value['key'] ]['composite_child_products']['parent_quantity'] = $parent_quantity;
							$woocommerce->cart->set_session();

							add_action('woocommerce_before_calculate_totals', array( $this, 'afcpb_add_component_products' ), 10, 1);
						}
					}
				}
			}
		}

		public function afcpb_get_cart_item_from_session( $cart_item, $values ) {

			if ( $cart_item['data']->is_type('af_composite_product') ) {
				$cart_item['data']->set_price( $this->composite_product_price_calculate( $cart_item['data']->get_id() , $cart_item['key'] ) );
			}

			return $cart_item;
		}

		public function afcpb_remove_cart_item( $cart_item_key, $cart ) {
			$cart_obj = wc()->cart->get_cart();

			foreach ($cart_obj as $cart_value ) {

				if ( array_key_exists( 'composite_child_products' , $cart_value ) ) {
					$parent_key = $cart_value['composite_child_products']['parent_id'];

					if ( $parent_key == $cart_item_key ) {
						WC()->cart->remove_cart_item($cart_value['key']);
					}
				}
			}
		}

		public function composite_product_price_calculate( $product_id, $cart_key ) {
			$product_price    = wc_get_product($product_id)->get_price();
			$cart             = wc()->cart->get_cart();
			$price_type       = get_post_meta( $product_id , 'af_comp_product_price_type' , true );
			$item_product_qty = 1;
			if ( 'calculated_price' == $price_type ) {
				$product_price = 0;

				foreach ($cart as $cart_value ) {
					if ( $cart_key == $cart_value['key'] ) {
						$item_product_qty = $cart_value['quantity'];
					}

					if ( array_key_exists( 'composite_child_products' , $cart_value ) ) {
						$child_arr = $cart_value['composite_child_products'];

						if ( $cart_key == $child_arr['parent_id'] ) {
							$comp_key       = $child_arr['comp_key'];
							$product_price += floatval( $cart_value['quantity'] ) * floatval( af_cp_component_product_price( $product_id , $cart_value['data']->get_id() , $comp_key , wc_get_product( $cart_value['data']->get_id() )->get_price() ));
						}
					}
				}
			}
			$adjust_type   = get_post_meta( $product_id , 'af_comp_product_price_adjustment' , true );
			$adjust_value  = floatval(get_post_meta( $product_id , 'af_comp_product_adj_value' , true ));
			$product_price = $product_price/$item_product_qty;
			if ( ( 0 != $adjust_value ) && ( '' != $adjust_value ) ) {
				$percentage = ( $product_price/100 ) * $adjust_value;

				if ( 'fixed_increase' == $adjust_type ) {
					$product_price += $adjust_value;

				} elseif ( 'fixed_decrease' == $adjust_type ) {
					$product_price = $product_price - $adjust_value;

				} elseif ( 'percentage_increase' == $adjust_type ) {
					$product_price = $product_price + $percentage;

				} elseif ( 'percentage_decrease' == $adjust_type ) {
					$product_price = $product_price - $percentage;

				}
			}
			if ( 0 > $product_price ) {
				$product_price = 0;
			}
			return $product_price;
		}

		public function check_if_component_is_already_added( $key, $comp_product_id, $comp_key, $variation_id, $variation_attr ) {
			$cart = wc()->cart->get_cart();

			foreach ($cart as $cart_key => $value) {
				if ( wc_get_product($comp_product_id)->is_type('variable') ) {

					if ( ( array_key_exists( 'composite_child_products' , $value ) ) && ( $comp_product_id == $value['product_id'] ) && ( $variation_id == $value['variation_id'] ) ) {
						$composite_product = $value['composite_child_products'];

						if ( ( $key == $composite_product['parent_id'] ) && ( $comp_key == $composite_product['comp_key'] ) ) {
							$check_for_attr = false;

							foreach ( (array) $value['variation'] as $key => $child_value) {

								if ( isset($variation_attr[ $key ]) ) {

									if ( $child_value == $variation_attr[ $key ] ) {

										$check_for_attr = true;
									}
								}
							}

							if ( $check_for_attr ) {
								return false;
							}
						}
					}
				} elseif ( ( array_key_exists( 'composite_child_products' , $value ) ) && ( $comp_product_id == $value['data']->get_id() ) ) {

						$composite_product = $value['composite_child_products'];

					if ( ( $key == $composite_product['parent_id'] ) && ( $comp_key == $composite_product['comp_key'] ) ) {

						return false;
					}
				}
			}
			return true;
		}

		public function afcpb_add_cart_item_data_fn_cb( $cart_item_data, $product_id, $variation_id ) {

			return Af_Composite_Product_Helper::afcpb_add_cart_item_meta($cart_item_data, $product_id, $variation_id);
		}

		public function afcpb_validation_min_max_product_quantity( $args, $product ) {
			$min   =  get_post_meta( $product->get_id() , 'af_comp_product_adj_min_qty' , true );
			$max   = get_post_meta( $product->get_id() , 'af_comp_product_adj_max_qty' , true );
			$steps = get_post_meta( $product->get_id() , 'af_comp_product_adj_step_qty' , true );
			
			if (isset($args['max_value']) && $args['max_value'] > 0 && '' != $args['max_value'] && ( ( $min <= $max ) || ( '' == $min ) )) {
				$max = min($args['max_value'], $max);
			}

			if ( '' != $min ) {
				$args['min_value'] = (int) $min;
			}

			if ( '' != $steps ) {
				$args['step'] = (int) $steps;
			}

			if ( '' != $max ) {
				if ( ( ( '' != $min ) && ( $min <= $max ) ) || ( '' == $min ) ) {
					$args['max_value'] = $max;
				}
			}

			if ( is_cart() || is_checkout() ) {
				global $woocommerce;
				$cart = wc()->cart->get_cart();

				foreach ( $cart as $cart_value ) {

					if ( ( isset($cart_value['composite_child_products'] ) )  && ( $cart_value['data']->get_id() == $product->get_id() ) ) {
						
						$parent_qty   = (int) $cart[ $cart_value['composite_child_products']['parent_id'] ]['quantity'];
						$find_parent  = $cart_value['composite_child_products']['parent_product_id'];
						$comp_key     = $cart_value['composite_child_products']['comp_key'];
						$return_array = (array) af_cp_child_comp_qty_array( $find_parent , $comp_key );

						$type = $return_array['type'];
						$min  = $return_array['min'];
						$max  = $return_array['max'];

						if ( 'range' == $type ) {
							if ( ( ( '' != $max ) ) && ( floatval($min) <= floatval($max) ) ) {
								$args['max_value'] = (int) $max * $parent_qty;
							}

							$args['min_value'] = (int) $min * $parent_qty;

							$args['step'] = (int) $parent_qty;

						}
						
					}
				}
			}
			return $args;
		}

		public function afcpb_show_price_cart_basket( $price, $cart_item, $cart_item_key ) {
			$cart_all = wc()->cart->get_cart();

			if ( ( !array_key_exists( 'composite_child_products' , $cart_item ) ) && ( !$cart_item['data']->is_type('af_composite_product') ) ) {
				return $price;
			}

			if ( $cart_item['data']->is_type('af_composite_product') ) {
				return wc_price($this->composite_product_price_calculate( $cart_item['data']->get_id() , $cart_item_key ));
			}

			if ( array_key_exists( 'composite_child_products' , $cart_item ) ) {
				$parent_id  = $cart_item['composite_child_products']['parent_product_id'];
				$price_type = get_post_meta( $parent_id , 'af_comp_product_price_type' , true );

				if ( 'calculated_price' == $price_type ) {
					$comp_key = $cart_item['composite_child_products']['comp_key'];

					return wc_price(af_cp_component_product_price( $parent_id , $cart_item['data']->get_id() , $comp_key , wc_get_product( $cart_item['data']->get_id() )->get_price() ));
				} else {

					return wc_price(0);
				}
			}
			return $price;
		}

		public function afcpb_cart_product_name( $item_name, $cart_item, $cart_item_key ) {
			$cart_item_keys = $cart_item;

			if ( !isset($cart_item['composite_child_products']) ) {
				return $item_name;
			}

			$parent_id  = $cart_item['composite_child_products']['parent_product_id'];
			$comp_key   = $cart_item['composite_child_products']['comp_key'];
			$comp_names = (array) get_post_meta( $parent_id , 'af_comp_product_component_name' , true );
			$name_keys  = (array) array_keys( $comp_names );
			$name       = '';

			if ( in_array( $comp_key , $name_keys ) ) {
				$name = $comp_names[ $comp_key ];
			}

			return '<strong>' . esc_html__( $name , 'af_comp_product' ) . '</strong><br>' . $item_name;
		}

		public function afcpb_order_product_name( $item_name, $item ) {
			$current_items_meta_array = $item->get_meta_data();
			foreach ($current_items_meta_array as $current_items_meta ) {
				if ( is_object($current_items_meta) ) {
					$item_meta_data = $current_items_meta->get_data();
					if ( isset($item_meta_data['key']) ) {
						if ( 'af_cp_component_name' == $item_meta_data['key'] ) {
							return '<strong>' . esc_html__( $item_meta_data['value']['name'] , 'af_comp_product' ) . '</strong><br>' . $item_name;
						}
					}
				}
			}
			return $item_name;
		}

		public function afcpb_order_formatted_line_subtotal_filter( $subtotal, $item, $that ) {
			$current_items_meta_array = $item->get_meta_data();
			foreach ($current_items_meta_array as $current_items_meta ) {
				if ( is_object($current_items_meta) ) {
					$item_meta_data = $current_items_meta->get_data();
					if ( isset($item_meta_data['key']) ) {
						if ( 'af_cp_component_name' == $item_meta_data['key'] ) {
							return '';
						}
					}
				}
			}
		
			return $subtotal;
		}

		public function afcpb_update_order_item_meta( $item, $cart_item_key, $values, $order ) {
			if ( ! isset( $values['composite_child_products'] ) ) {
				return;
			}
		
			$parent_id  = $values['composite_child_products']['parent_product_id'];
			$comp_key   = $values['composite_child_products']['comp_key'];
			$comp_names = (array) get_post_meta( $parent_id , 'af_comp_product_component_name' , true );
			$name_keys  = (array) array_keys( $comp_names );
			$name       = '';

			if ( in_array( $comp_key , $name_keys ) ) {
				$name = $comp_names[ $comp_key ];
			}
			
			$item->update_meta_data( __('af_cp_component_name'), array( 'name' => $name ) );
		
			return $item;
		}

		public function afcpb_restrict_user_to_remove_item( $button_link, $cart_item_key ) {
			$cart_all = WC()->cart->get_cart();

			if ( isset($cart_all[ $cart_item_key ]) ) {

				if (array_key_exists('composite_child_products', $cart_all[ $cart_item_key ])) {
					return '';
				}
			}
			return $button_link;
		}
	
		public function afcpb_cart_product_subtotal( $price, $cart_item, $cart_item_key ) {

			if ( array_key_exists( 'composite_child_products' , $cart_item ) ) {
				return '';
			}
			return $price;
		}

		public function afcpb_disable_quantity_in_cart( $product_quantity, $cart_item_key ) {
			$cart_all = WC()->cart->get_cart();
			if ( isset($cart_all[ $cart_item_key ]) ) {

				if (array_key_exists('composite_child_products', $cart_all[ $cart_item_key ])) {

					$find_parent = $cart_all[ $cart_item_key ]['composite_child_products']['parent_product_id'];
					$comp_key    = $cart_all[ $cart_item_key ]['composite_child_products']['comp_key'];
					
					if ( !isset($cart_all[ $cart_all[ $cart_item_key ]['composite_child_products']['parent_id'] ]) ) {
						return $product_quantity;
					}

					$parent_qty   = (int) $cart_all[ $cart_all[ $cart_item_key ]['composite_child_products']['parent_id'] ]['quantity'];
					$return_array = (array) af_cp_child_comp_qty_array( $find_parent , $comp_key );
					$type         = $return_array['type'];
					$allow_edit   = get_post_meta( $find_parent , 'af_cp_allow_edit_in_cart' , true );

					if ( ( 'fixed' == $type ) || ( 'yes' != $allow_edit ) ) {
						return $cart_all[ $cart_item_key ]['quantity'];
					}

					$type = $return_array['type'];
					$max  = '';
					if ( '' != $return_array['max'] ) {
						$max = floatval($return_array['max']) * $parent_qty;
					}
					$min = 0;
					if ( '' != $return_array['min'] ) {
						$min = floatval( $return_array['min'] ) * $parent_qty;
					}


					$product_quantity = '<div class="quantity">
										 <label class="screen-reader-text" for="quantity_' . $cart_item_key . '">' . esc_html__( get_the_title($find_parent) . ' quantity' , 'af_comp_product' ) . '</label>
										 <input type="number" 
											id="quantity_' . $cart_item_key . '" 
												class="input-text qty text" 
												step="' . $parent_qty . '" 
												min="' . $min . '" 
												max="' . $max . '" 
												name="cart[' . $cart_item_key . '][qty]" 
												value="' . $cart_all[ $cart_item_key ]['quantity'] . '" title="Qty" size="4" placeholder="" inputmode="numeric" autocomplete="off" />
									     </div>';
				}
			}
			return $product_quantity;
		}

		public function check_configurable_in_cart( $cart ) {

		  $flag ='not_found';

			foreach ( $cart as $cart_item ) {

				if ( $cart_item['data']->is_type('af_composite_product')  ) {

				  $flag='found';
				  break;
				}

			}

		 return $flag;
		}

		
		public function get_shipping_all_cart_item( $all_package_content ) {

		$cart_item_keys = array();

			foreach ($all_package_content as $cart_key => $cart_item_data) {
				if (isset($cart_item_data['composite_child_products'])) {
					if (!empty($cart_item_data['composite_child_products']['parent_product_id'])) {
						$afcb_get_parent_product_id = (int) $cart_item_data['composite_child_products']['parent_product_id'];

						$shipping_package_type = get_post_meta($afcb_get_parent_product_id, 'af_cp_shipping_page_type', true);

						$af_cb_parent_cart_item_key = $cart_item_data['composite_child_products']['parent_id'];

						if ( 'composite' == $shipping_package_type) {
							$cart_item_keys[] = $cart_key;
						} else if ('composite' != $shipping_package_type) {
							$cart_item_keys[] = $af_cb_parent_cart_item_key;
						}
					}
				}
			}

		$cart_item_keys = array_unique($cart_item_keys);

		return $cart_item_keys;
		}

		public function set_shipping_fee_zero_for_specific_cart_item( $rates, $package ) {

		 $cart = wc()->cart->get_cart();

		 $afcb_get_cart_items_to_exclude_line_shipping=array();
	
			if ('found'==( $this->check_configurable_in_cart($cart) )) {

				if (!empty(( $this->get_shipping_all_cart_item($package['contents']) ))) {

					 $afcb_get_cart_items_to_exclude_line_shipping = (array) ( $this->get_shipping_all_cart_item($package['contents']) );

				}
	
			}

			if (!empty($afcb_get_cart_items_to_exclude_line_shipping)) {
		
				foreach ($package['contents'] as $cart_key => $cart_item_data) {

					foreach ($afcb_get_cart_items_to_exclude_line_shipping as $excluded_key) {
						if ($cart_key === $excluded_key) {
				
						   $cart_item_data['data']->set_virtual(true);
						   $cart_item_data['data']->set_downloadable(true);
						   $cart_item_data['data']->set_weight( '' );
						}
					}

				}

			}
	
	return $rates;
		}
				
		public function afcpb_specific_products_shipping_methods( $packages ) {
	
			$cart         = wc()->cart->get_cart();
			$items_string = array();

			foreach ( $cart as $cart_item ) {
				
				$product_id = $cart_item['data']->get_id();
				
				if ( $cart_item['data']->is_type('af_composite_product')  ) {
					$shipping_package_type = get_post_meta( $product_id , 'af_cp_shipping_page_type' , true );
					if ( 'composite' == $shipping_package_type ) {
						$items_string[] =  get_the_title($cart_item['data']->get_id()) . ' &times; ' . $cart_item['quantity'];
					}

				} elseif ( isset( $cart_item['composite_child_products'] ) ) {
					$find_parent           = $cart_item['composite_child_products']['parent_product_id'];
					
					$shipping_package_type = get_post_meta( $find_parent , 'af_cp_shipping_page_type' , true );

					if ( 'composite' != $shipping_package_type ) {
						$items_string[] =  get_the_title($cart_item['data']->get_id()) . ' &times; ' . $cart_item['quantity'];
					}
									
				} else {
					$items_string[] =  get_the_title($cart_item['data']->get_id()) . ' &times; ' . $cart_item['quantity'];
				}
			}
			$af_cp_shipping_str = implode( ',' , $items_string );
			foreach ( $packages as $key => $value) {
				$flat_rates = $value->get_meta_data();
				foreach ( $flat_rates as $flat_rates_key => $flat_rates_value ) {
					$value->add_meta_data( $flat_rates_key , $af_cp_shipping_str );
				}
				
			}
			return $packages;
		}

		public function afcpb_theme_dependent_styling() {
			?>
			<style type="text/css">
			<?php
				$current_theme = wp_get_theme();
				$parent_theme  = $current_theme->parent();

			if ($current_theme->get('Name') === 'Twenty Twenty-Five' || ( $parent_theme && $parent_theme->get('Name') === 'Twenty Twenty-Five' )) {
				?>
				#afcp-add-to-cart-product{
					margin-right: 10px;
					margin-top: 10px;
				}
				.wp-block-button__link {
					height: auto !important;
				}
			<?php } ?>

			<?php
		}
	}
	new ADF_Composite_Product_Front();
}


