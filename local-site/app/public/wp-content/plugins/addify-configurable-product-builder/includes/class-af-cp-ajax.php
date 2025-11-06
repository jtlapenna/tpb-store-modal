<?php
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'ADF_Composite_Product_Ajax_Fns' ) ) {
	class ADF_Composite_Product_Ajax_Fns {

		public function __construct() {

			add_action( 'wp_ajax_af_comp_product_live_search', array( $this, 'afcp_product_live_search' ) );
			add_action( 'wp_ajax_nopriv_af_comp_product_live_search', array( $this, 'afcp_product_live_search' ) );  

			add_action( 'wp_ajax_af_comp_default_product_live_search', array( $this, 'af_comp_default_product_live_search_cb' ) );
			add_action( 'wp_ajax_nopriv_af_comp_default_product_live_search', array( $this, 'af_comp_default_product_live_search_cb' ) );  
			
			add_action( 'wp_ajax_af_composite_product_add_component_ajax', array( $this, 'af_composite_product_add_component_ajax_cb' ) );
			add_action( 'wp_ajax_nopriv_af_composite_product_add_component_ajax', array( $this, 'af_composite_product_add_component_ajax_cb' ) );  
			
			add_action( 'wp_ajax_af_cp_add_scenario_actions_ajax', array( $this, 'af_cp_add_scenario_actions_ajax_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_add_scenario_actions_ajax', array( $this, 'af_cp_add_scenario_actions_ajax_cb' ) );  

			add_action( 'wp_ajax_af_cp_add_component_products_ajax', array( $this, 'af_cp_add_component_products_ajax_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_add_component_products_ajax', array( $this, 'af_cp_add_component_products_ajax_cb' ) );  
			
			add_action( 'wp_ajax_af_cp_add_actions_scenario_ajax', array( $this, 'af_cp_add_actions_scenario_ajax_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_add_actions_scenario_ajax', array( $this, 'af_cp_add_actions_scenario_ajax_cb' ) );  

			add_action( 'wp_ajax_af_cp_add_scenario_condition_ajax', array( $this, 'af_cp_add_scenario_condition_ajax_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_add_scenario_condition_ajax', array( $this, 'af_cp_add_scenario_condition_ajax_cb' ) );  
			
			add_action( 'wp_ajax_af_cp_on_select_scenarios_ajax', array( $this, 'af_cp_on_select_scenarios_ajax_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_on_select_scenarios_ajax', array( $this, 'af_cp_on_select_scenarios_ajax_cb' ) );  

			add_action( 'wp_ajax_af_cp_review_steps_selection', array( $this, 'af_cp_review_steps_selection_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_review_steps_selection', array( $this, 'af_cp_review_steps_selection_cb' ) );  

			add_action( 'wp_ajax_af_cp_on_select_change_ajax', array( $this, 'af_cp_on_select_change_ajax_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_on_select_change_ajax', array( $this, 'af_cp_on_select_change_ajax_cb' ) );  

			add_action( 'wp_ajax_af_cp_thumbnail_pagination', array( $this, 'af_cp_thumbnail_pagination_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_thumbnail_pagination', array( $this, 'af_cp_thumbnail_pagination_cb' ) );  
			
			add_action( 'wp_ajax_af_cp_sort_selection', array( $this, 'af_cp_sort_selection_cb' ) );
			add_action( 'wp_ajax_nopriv_af_cp_sort_selection', array( $this, 'af_cp_sort_selection_cb' ) );  
		}

		public function af_cp_on_select_scenarios_ajax_cb() {

			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}
			
			$form_table = array();

			$form_variation_ids = array();

			if (isset($_POST['form_data'])) {
				parse_str( sanitize_meta( '', $_POST['form_data'], '' ), $form_table );
				$form_table = sanitize_meta( '', wp_unslash( $form_table ), '' );
				if (!empty($form_table)) {

					$form_variation_ids = isset($form_table['af_cp_variation_id'])?$form_table['af_cp_variation_id']:array();
				}
				
			}
			$current_key     = sanitize_text_field( isset($_POST['current_key']) ? $_POST['current_key'] : '');
			$af_cp_sce_class = sanitize_text_field( isset($_POST['af_cp_sce_class']) ? $_POST['af_cp_sce_class'] : '');
			$product_id      = sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');

			if ( !wc_get_product($product_id) ) {
				die('');
			}
			$current_product_obj = wc_get_product($product_id);
			$components_in_form  = array();

			if ( isset( $form_table['af_cp_component_product'] ) ) {
				$components_in_form = (array) $form_table['af_cp_component_product'];
			}

			$comp_all_keys = (array) array_keys($components_in_form);
			$first_comp    = current($comp_all_keys);

			$scenario_validate       = (array) af_cp_validate_scenario_fn( $product_id , $form_table );
			$return_hidden_class_arr = (array) $scenario_validate['hide'];
			$all_comp_products       = $scenario_validate['options'];




			$return_hidden_class     = array();
			foreach ( $return_hidden_class_arr as $key => $value) {

				// if ( $first_comp == $value ) {
				//  unset($return_hidden_class_arr[ $key ]);
				//  continue;
				// }
				$return_hidden_class[] = $product_id . '_' . $value;
			}

			$return_replace                = array();
			$return_selected_products_html = array();
			
			$return_replace_option_changed = array();

			
			
			foreach ( $all_comp_products as $comp_key => $filtered_products ) {
				$sep_comp_products       = (array) af_cp_component_products( $product_id, $comp_key );
				$check_sep_comp_products = false;
				$component_product_option_changed = false;

		

				
				// if ( $first_comp == $comp_key ) {
				//  continue;
				// }

				if ( $current_key == $comp_key ) {
					continue;
				}

				if ( in_array( $comp_key , $return_hidden_class_arr ) ) {
					continue;
				}

				$default_product = af_cp_component_default_product( $product_id , $comp_key );

				if ( !in_array( $default_product , (array) $filtered_products ) ) {
					$default_product = current($filtered_products);
					$component_product_option_changed = true;
				}
				if ( 'yes' == $af_cp_sce_class ) {
					if ( isset( $components_in_form[ $comp_key ] ) ) {
						$default_product = $components_in_form[ $comp_key ];
					}
				}

				$list_style            = 'simple';
				$af_cp_component_style = (array) get_post_meta( $product_id , 'af_composite_product_component_style' , true );

				if ( isset($af_cp_component_style[ $comp_key ]) ) {
					$list_style = $af_cp_component_style[ $comp_key ];
				}
				$af_cp_quantity_op = (array) get_post_meta( $product_id , 'af_composite_product_quantity_op' , true );
				$qty_option        = '';

				if ( isset($af_cp_quantity_op[ $comp_key ]) ) {
					$qty_option = $af_cp_quantity_op[ $comp_key ];
				}
				$fixed_qty_text = '';
				$fixed_qty      = 1;

				if ( 'fixed' == $qty_option ) {
					$fixed_qty_op = (array) get_post_meta( $product_id , 'af_composite_product_fixed_qty' , true );

					if ( isset($fixed_qty_op[ $comp_key ]) ) {
						$fixed_qty = $fixed_qty_op[ $comp_key ];
					}

					if ( '' == $fixed_qty ) {
						$fixed_qty = 1;
					}
					$fixed_qty_text = $fixed_qty . ' &times; ';
				}
									
					ob_start();
						af_cp_selected_product_html( $filtered_products , $product_id , $default_product , $comp_key , array() , $form_variation_ids );
					$return_selected_products_html[ 'af_cp_selected_product_' . $product_id . '_' . $comp_key ] = ob_get_clean();

					ob_start();

				if ( 'simple' == $list_style ) {
					af_cp_simple_dropdown( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option );

				} elseif ( 'image_product' == $list_style ) {
					af_cp_image_dropdown( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option );

				} elseif ( 'radio' == $list_style ) {
					af_cp_radio_options( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option );
				} elseif ( 'thumbnail' == $list_style ) {

					af_cp_thumbnail_options( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option , 1 );
				}

			//  if ($component_product_option_changed) {
					$return_replace_option_changed[ 'af_cp_select_product_' . $product_id . '_' . $comp_key ] = ob_get_clean();
			//  }
					// af_cp_component_common_qty( $product_id , $qty_option , $comp_key , $fixed_qty );

				$return_replace[ 'af_cp_select_product_' . $product_id . '_' . $comp_key ] = ob_get_clean();


			}

		
			
			wp_send_json( 
				array(
					'id'       => $product_id,
					'hide'     => $return_hidden_class,
					'replace'  => $return_replace,
					'seniro_replace'=>$return_replace_option_changed,
					'selected' => $return_selected_products_html,
					'new_btn'  => $this->af_cp_scenario_btn_ajax( $product_id , $return_hidden_class_arr , $all_comp_products ),
				)
				);
			die();
		}

		public function af_cp_scenario_btn_ajax( $product_id, $return_hidden_class_arr, $all_comp_products ) {
			$af_cp_comp_name = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
			$new_btn_html    = array();
			foreach ( $af_cp_comp_name as $key => $value) {
				if ( in_array( $key , (array) $return_hidden_class_arr ) ) {
					unset($af_cp_comp_name[ $key ]);
				}
			}
			foreach ( $af_cp_comp_name  as $comp_key => $comp_name ) {
				$comp_products = array();
				if ( isset($all_comp_products[ $comp_key ]) ) {
					$comp_products = $all_comp_products[ $comp_key ];
				}
				if ( empty($comp_products) ) {
					continue;
				}
				$name_keys         = array_keys($af_cp_comp_name);
				$current_key_index = array_search( $comp_key , $name_keys );
				$last_key_index    = end($name_keys);
				$prev_key_index    = '';
				
				$count_prev = 0;
				if ( 0 <= ( $current_key_index - 1 ) ) {
					$prev_key_index =  $name_keys[ $current_key_index - 1 ];
				}
					$next_key_index = '';
				if ( count( $name_keys ) > ( $current_key_index + 1 ) ) {
					$next_key_index = $name_keys[ $current_key_index + 1 ];
				}
					$review_layout = false;
				if ( count($name_keys) == ( $current_key_index + 1 ) ) {
					$review_layout = true;
				}
				ob_start();
				?>
					
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
					<?php
						$new_btn_html[ 'bottom_btn_step_' . $product_id . '_' . $comp_key ] = ob_get_clean();
			}
			return $new_btn_html;
		}

		public function af_cp_review_steps_selection_cb() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$form_table = array();

			if (isset($_POST['form_data'])) {
				parse_str( sanitize_meta( '', $_POST['form_data'], '' ), $form_data );
				$form_table = sanitize_meta( '', wp_unslash( $form_data ), '' );
			}

			$product_id = sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');

			if ( !wc_get_product($product_id) ) {
				die('');
			}
			$current_product_obj = wc_get_product($product_id);

			$components_in_form = array();

			if ( isset( $form_table['af_cp_component_product'] ) ) {
				$components_in_form = (array) $form_table['af_cp_component_product'];
			}
			$qty_in_form = array();

			if ( isset( $form_table['af_cp_component_product_qty'] ) ) {
				$qty_in_form = (array) $form_table['af_cp_component_product_qty'];
			}

			$af_cp_comp_name = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
			
			ob_start();
			?>
				<h3><?php echo esc_html__( 'Review your selections' , 'af_comp_product'); ?></h3>
					<?php

					$scenario_validate       = (array) af_cp_validate_scenario_fn( $product_id , $form_table );
					$return_hidden_class_arr = $scenario_validate['hide'];
					$return_hidden_class     = array();
					foreach ( $return_hidden_class_arr as $key => $value) {

						// if ( $first_comp == $value ) {
						//  continue;
						// }
						$return_hidden_class[] = $value;
					}
					foreach ( $components_in_form as $comp_key => $value ) {

						if ( ( !wc_get_product($value) ) || ( in_array( $comp_key , $return_hidden_class ) ) ) {
							continue;
						}


						$comp_name = '';

						if ( isset($af_cp_comp_name[ $comp_key ]) ) {
							$comp_name = $af_cp_comp_name[ $comp_key ];
						}

						$current_quantity = 1;

						if ( isset($qty_in_form[ $comp_key ]) ) {
							$current_quantity = $qty_in_form[ $comp_key ];
						}

						?>
							<div class="title review-div_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>">
								<a class="af_cp_steps_btn " data-val="single_comp_step_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>">
									<?php echo esc_html__( $comp_name , 'af_comp_product'); ?>
								</a>
							</div>
							<div class="selected_product review-div_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>  af_cp_selected_product_<?php echo esc_attr($product_id); ?>_<?php echo esc_attr($comp_key); ?>">
							<?php

								$image          = current( (array) wp_get_attachment_image_src( get_post_thumbnail_id( $value ), 'single-post-thumbnail' ));
								$product        = wc_get_product($value);
								$show_title_arr = (array) get_post_meta( $product_id , 'af_cp_selection_title_cb' , true );
								$title_keys     = (array) array_keys( $show_title_arr );
								$show_title     = '';

							if ( in_array( $comp_key , $title_keys ) ) {
								$show_title = $show_title_arr[ $comp_key ];
							}
		
								$show_desc_arr = (array) get_post_meta( $product_id , 'af_cp_selection_desc_cb' , true );
								$desc_keys     = (array) array_keys( $show_desc_arr );
								$show_desc     = '';

							if ( in_array( $comp_key , $desc_keys ) ) {
								$show_desc = $show_desc_arr[ $comp_key ];
							}
									
								$show_thumbnail_arr = (array) get_post_meta( $product_id , 'af_cp_selection_thumbnail_cb' , true );
								$thumbnail_keys     = (array) array_keys( $show_thumbnail_arr );
								$show_thumbnail     = '';

							if ( in_array( $comp_key , $thumbnail_keys ) ) {
								$show_thumbnail = $show_thumbnail_arr[ $comp_key ];
							}
									
								$show_price_arr = (array) get_post_meta( $product_id , 'af_cp_selection_price_cb' , true );
								$price_keys     = (array) array_keys( $show_price_arr );
								$show_price     = '';

							if ( in_array( $comp_key , $price_keys ) ) {
								$show_price = $show_price_arr[ $comp_key ];
							}
								
								$show_stock_arr = (array) get_post_meta( $product_id , 'af_cp_selection_stock_cb' , true );
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
										
								$regular_price = af_cp_component_product_price( $product_id , $value , $comp_key , $loop_regular_price );
								$sale_price    = af_cp_component_product_price( $product_id , $value , $comp_key , $loop_sale_price );

								if ( $sale_price == $regular_price ) {
									$price = wc_price( $sale_price );
								} else {
									$price = '<del>' . wc_price( $regular_price ) . '</del> <ins>' . wc_price( $sale_price ) . '</ins>' ;
								}
							}

							?>
									<div class="image review-div_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>">
									<?php

									if ( 'yes' == $show_thumbnail ) {

										if ( '' == $image ) {
											$image = AFCPB_DIR_URL . '/includes/assets/images/image-placeholder.jpg';
										}
										?>
											<img src="<?php echo esc_url($image); ?>" data-id="<?php echo esc_attr($value); ?>">
											<?php
									}
									?>
									</div>
									<div class="detail review-div_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>">
									<?php

									if ( 'yes' == $show_title ) {
										?>
												<h4 class="product_title"><?php echo esc_attr($current_quantity) . ' &times; ' . esc_html__( get_the_title($value) , 'af_comp_product'); ?></h4>
											<?php
									}

									if ( 'yes' == $show_price ) {
										?>
												<p class="af_cp_price"><?php echo wp_kses_post( $price ); ?></p>
											<?php
									}

									if ( 'yes' == $show_desc ) {
										?>
												<p><?php echo wp_kses_post( $product->get_short_description() ); ?></p>
											<?php
									}

									if ( 'yes' == $show_stock ) {
										?>
											<span class="stock_msg"><?php echo wp_kses_post( af_cp_get_stock_msg_with_qty($value) ); ?></span>
											<?php
									}

									?>
									</div>
								<?php

								if ( $product->is_type('variable') ) {
									$attributes = $product->get_variation_attributes();

									foreach ( $attributes as $attribute_name => $options ) {
										$selected_attribute      = '';
										$selected_attribute_name = strtolower( 'attribute_' . $attribute_name );

										if ( isset( $form_table[ $selected_attribute_name ] ) ) {
											$attribute_sel_keys = (array) array_keys( (array) $form_table[ $selected_attribute_name ]);

											if ( in_array( $comp_key , $attribute_sel_keys ) ) {
												$selected_attribute = $form_table[ $selected_attribute_name ][ $comp_key ];
											}
										}

										if ( '' === $selected_attribute ) {
											?>
											<p class="woocommerce-info"><?php echo esc_html__( 'Please  ' , 'af_comp_product'); ?>&nbsp;
												<a class="af_cp_steps_btn" data-val="single_comp_step_<?php echo esc_attr( $product_id . '_' . $comp_key); ?>" >
													<?php echo esc_html__( ' Click here  ' , 'af_comp_product'); ?>
												</a>&nbsp;
												<?php echo esc_html__( ' to select product options' , 'af_comp_product'); ?>
											</p>
											<?php
										}

										break;
									}
								}
								?>
							</div>
							<div class="af_cp_comp_messages review-div_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?> af_cp_component_product_<?php echo esc_attr( $product_id . '_' . $comp_key ); ?>_errors"></div>
							<?php
					}   

					$return = ob_get_clean();

					ob_start();
					$name_keys     = (array) array_keys($af_cp_comp_name);
					$last_comp     = array_search( end($name_keys) , $name_keys );
					$last_comp_key = '';

					if ( isset($name_keys[ $last_comp ]) ) {
						$last_comp_key = $name_keys[ $last_comp ];
					}

					?>
				<a class="af_cp_steps_btn " data-val="single_comp_step_<?php echo esc_attr($product_id . '_' . $last_comp_key ); ?>"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;<?php echo esc_html__(  end($af_cp_comp_name) , 'af_comp_product'); ?></a>
			<?php
			$last_key = ob_get_clean();
			wp_send_json( 
				array(
					'data'     =>    $return,
					'class'    =>    'single_comp_step_' . $product_id . '_' . $last_comp_key,
					'last_key' =>    $last_key,
				)
				);
			die();
		}

		public function afcp_product_live_search() {

			$nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}
			
			$return = array(); 
			
			if (isset($_GET['q'])) {
				$search =  sanitize_text_field( wp_unslash( $_GET['q'] ));
			}

			$search_results = new WP_Query(
			array(
				's'              => $search, 
				'post_type'      => array( 'product', 'product_variation' ), 
				'post_status'    => 'publish', 
				'posts_per_page' => -1, 
			)
			);

			if ( $search_results->have_posts() ) :

				while ( $search_results->have_posts() ) :
					$search_results->the_post();
					$product = wc_get_product($search_results->post->ID);

					// if ( $product->is_type('simple') || $product->is_type('variation') || $product->is_type('variable') ) {
					//  $title    = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
					//  $return[] = array( $search_results->post->ID, $title ); 
					// }


					if ( $product->is_type('simple') || $product->is_type('variable') ) {
						$title    = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
						$return[] = array( $search_results->post->ID, $title ); 
					} else if ( $product->is_type('variation') ) {
						$variation_attributes = $product->get_variation_attributes();
						
						// Check if all attributes are non-empty
						$has_empty_attribute = false;
						foreach ($variation_attributes as $attribute) {
							if ( empty($attribute) ) {
								$has_empty_attribute = true;
								break;
							}
						}
						
						// If any attribute is empty, skip this iteration
						if ( $has_empty_attribute ) {
							continue;
						}
					
						$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
						$return[] = array( $search_results->post->ID, $title ); 
					}
					
				endwhile;
			endif;
			wp_send_json( $return );
		}

	
		
		
		public function af_comp_default_product_live_search_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$all_products = array();
			$return       = array();

			if ( isset($_POST['default_product']) ) {
				$default_product =  sanitize_meta('', $_POST['default_product'] , '' );
			} else {
				$default_product = '';
			}

			if ( isset($_POST['products']) ) {
				$products     = (array) sanitize_meta('', $_POST['products'] , '' );
				$all_products = array_merge( (array) $products , (array) $all_products );
				$all_products = array_unique($all_products);

			}

			if ( isset($_POST['cats']) ) {
				$cats =  sanitize_meta('', $_POST['cats'] , '' );

				if ( is_array($cats) && !empty($cats) ) {
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
					$all_cats              =  get_posts($all_cats);
					$all_products          = array_merge( $all_cats , $all_products );
					$all_products = array_unique($all_products);

				}
			}

			if ( isset($_POST['tags']) ) {
				$tags =  sanitize_meta('', $_POST['tags'] , '' );

				if ( is_array($tags) && !empty($tags) ) {
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
					$all_tags              =  get_posts($all_tags);
					$all_products          = array_merge( $all_tags , $all_products );
					$all_products          = array_unique($all_products);

				}
			}

			if ( !empty($all_products) ) {
				$already_added = array();

				foreach ($all_products as $key => $value) {

					if ( ( 0 == $value ) || ( 1 == $value ) ) {
						continue;
					}

					if ( !wc_get_product($value) ) {
						continue;
					}

					$product = wc_get_product($value);

					if ( $product->is_type('af_composite_product') ) {
						continue;
					}

					if ( ( !$product->is_type('simple') ) && ( !$product->is_type('variable') ) && ( !$product->is_type('variation') ) ) {
						continue;
					}

					if ( in_array( $value , $already_added ) ) {
						continue;
					}

					$already_added[] = $value;

					if ( $product->is_type('variable') ) {

						if ( ( '' != $default_product ) && ( $default_product == $value ) ) {
							$return[] = '<option value="' . $value . '" selected>' . esc_html__( get_the_title($value) , 'af_comp_product' ) . '</option>';
						} else {
							$return[] = '<option value="' . $value . '" >' . esc_html__( get_the_title($value) , 'af_comp_product' ) . '</option>';
						}
						$variations = $product->get_children();

						foreach ($variations as $var_key => $var_id ) {
							$variation = wc_get_product( $var_id );

							if ( $variation->is_type('variation') ) {
								$var_obj = wc_get_product($var_id);

								if ( in_array( $var_obj->get_parent_id() , $all_products ) ) {
									break;
								}

								if ( in_array( $var_id , $already_added ) ) {
									continue;
								}

								$already_added[] = $var_id;

								if ( ( '' != $default_product ) && ( $default_product == $var_id ) ) {
									$return[] = '<option value="' . $var_id . '" selected>&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html__( get_the_title($var_id) , 'af_comp_product' ) . '</option>';
								} else {
									$return[] = '<option value="' . $var_id . '">&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html__( get_the_title($var_id) , 'af_comp_product' ) . '</option>';
								}
							}
						}
					} elseif ( ( '' != $default_product ) && ( $default_product == $value ) ) {

							$return[] = '<option value="' . $value . '" selected>' . esc_html__( get_the_title($value) , 'af_comp_product' ) . '</option>';
					} else {
						$return[] = '<option value="' . $value . '">' . esc_html__( get_the_title($value) , 'af_comp_product' ) . '</option>';
					}
				}
			}

			wp_send_json( $return );
		}

		public function af_cp_on_select_change_ajax_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$return_price   = 0;
			$low_stock_msg  = array();
			$error_messages = array();
			$allow_cart     = 'yes';
			$form_variation_ids = array();
			$form_table = array();
			
			if (isset($_POST['form_data'])) {
				parse_str( sanitize_meta( '', $_POST['form_data'], '' ), $form_table );
				$form_table = sanitize_meta( '', wp_unslash( $form_table ), '' );
				
				if (!empty($form_table)) {

					$form_variation_ids = isset($form_table['af_cp_variation_id'])?$form_table['af_cp_variation_id']:array();
				}
		
			} else {
				$form_table = array();
			}

			$current_product_id = sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');

			if ( !get_post($current_product_id) ) {
				die('');
			}
			
			$quantities = array();

			if ( isset( $form_table['af_cp_component_product_qty'] ) ) {
				$quantities = (array) $form_table['af_cp_component_product_qty'];
			}

			$check_double_product = false;
			$selected_products    = array();
			$double_exclusive     = array();
			
			$af_cp_comp_name             = (array) get_post_meta( $current_product_id , 'af_comp_product_component_name' , true );
			$current_product_obj         = wc_get_product($current_product_id);
			$quantity_of_current_product = 1;

			if ( isset( $form_table['quantity'] ) ) {
				$quantity_of_current_product = $form_table['quantity'];
			}
			
			$price_type          = get_post_meta( $current_product_id , 'af_comp_product_price_type' , true );
			$total_regular_price = af_cp_calculate_reg_sale_prices( $current_product_obj )['regular'];
			$total_sale_price    = af_cp_calculate_reg_sale_prices( $current_product_obj )['sale'];
			
			if ( 'calculated_price' == $price_type ) {
				$total_regular_price = 0;
				$total_sale_price    = 0;
			}
			
			$af_cp_variations = array();

			if ( isset( $form_table['af_cp_variation_id'] ) ) {
				$af_cp_variations = (array) $form_table['af_cp_variation_id'];
			}

			if ( isset( $form_table['af_cp_component_product'] ) ) {
				$products_in_form = (array) $form_table['af_cp_component_product'];

				if ( empty($products_in_form) ) {
					$check_double_product = true;
				}

				$scenario_validate       = (array) af_cp_validate_scenario_fn( $current_product_id , $form_table );
				$return_hidden_class_arr = (array) $scenario_validate['hide'];
				$return_products_arr     = (array) $scenario_validate['options'];

				foreach ( $products_in_form as $key =>  $product_id ) {

					if ( in_array( $key , $return_hidden_class_arr ) ) {
						continue;
					}

					$qty = 1;

					if ( isset($quantities[ $key ]) ) {
						$qty = $quantities[ $key ];
					}

					$product_obj    = wc_get_product( $product_id );
					$comp_name      = '';
					$comp_name_keys = (array) array_keys( $af_cp_comp_name );

					if ( in_array( $key , $comp_name_keys ) ) {
						$comp_name = $af_cp_comp_name[ $key ];
					}

					if ( $product_obj ) {
						
						$price_type = get_post_meta( $current_product_id , 'af_comp_product_price_type' , true );
						
						$exclusive_comp      = (array) get_post_meta( $current_product_id , 'af_composite_product_exclusive_cb' , true );
						$exclusive_component = '';

						ob_start();
							af_cp_selected_product_html( array() , $current_product_id , $product_id , $key , $form_table, $form_variation_ids );
						$selected_products[ 'af_cp_selected_product_' . $current_product_id . '_' . $key ] = ob_get_clean();

						if ( $product_obj->is_type('variable') ) {

							if ( !wc_get_product($product_id) ) {
								ob_start();
								?>
								<p class="woocommerce-error">
									<?php echo esc_html__( $comp_name . ' -> Select variation attributes first' , 'af_comp_product'); ?>
								</p>
								<?php
								$selected_products[ 'af_cp_selected_product_' . $current_product_id . '_' . $key ] = ob_get_clean();
							}
						}
						$var_ids_for_current      = (array) array_keys( $af_cp_variations );
						$variation_id_for_current = 0;

						if ( in_array( $key , $var_ids_for_current ) ) {
							$variation_id_for_current = $af_cp_variations[ $key ];
						}

						if ( 'calculated_price' == $price_type ) {
							$product_obj_for_price = $product_obj;

							if ( $product_obj->is_type('variable') ) {
								$product_obj_for_price = wc_get_product($variation_id_for_current);
							}

							if ( $product_obj_for_price ) {

								$regular_price        = af_cp_component_product_price( $current_product_id , $product_id , $key , $product_obj_for_price->get_regular_price() );
								$sale_price           = af_cp_component_product_price( $current_product_id , $product_id , $key , $product_obj_for_price->get_price() );
								
								if (!empty($product_obj_for_price->get_regular_price())) {
								
									$total_regular_price += floatval($regular_price) * floatval($qty);
									$total_sale_price    += floatval($sale_price) * floatval($qty);

								}
				
								

							}
						}
						
						if ( array_key_exists( $key , (array) $exclusive_comp ) ) {
							$exclusive_component = $exclusive_comp[ $key ];
						}

						if ( '' == $product_obj->get_price() ) {
							ob_start();
							?>
									<p class="woocommerce-error">
										<?php echo esc_html__( $comp_name . ' -> ' . get_the_title( $product_id ) . ' cannot be purchased at the moment' , 'af_comp_product'); ?>
									</p>
								<?php
								$double_exclusive[ 'af_cp_component_product_' . $current_product_id . '_' . $key . '_errors' ] = ob_get_clean();
								$check_double_product = true;
						}

						foreach ( $products_in_form as $child_key => $child_value ) {

							if ( ( $key != $child_key ) && ( $child_value == $product_id ) ) {

								if ( array_key_exists( $child_key , $quantities ) ) {
									$qty = $quantities[ $child_key ];
								}

								if ( in_array( $child_key , $return_hidden_class_arr ) ) {
									continue;
								}

								if ( 'yes' == $exclusive_component ) {
									$check_double_product = true;
									$child_comp_name      = '';
									$child_comp_name_keys = (array) array_keys( $af_cp_comp_name );

									if ( in_array( $child_key , $child_comp_name_keys ) ) {
										$child_comp_name = $af_cp_comp_name[ $child_key ];
									}

									ob_start();
									?>
										<p class="woocommerce-info">
											<?php echo esc_html__( $child_comp_name . ' -> ' . get_the_title( $product_id ) . ' is already selected in ' . $comp_name . ' can not select in any other component' , 'af_comp_product'); ?>
										</p>
										<?php
										$double_exclusive[ 'af_cp_component_product_' . $current_product_id . '_' . $child_key . '_errors' ] = ob_get_clean();
								}
							}

							if ( !array_key_exists( 'af_cp_component_product_' . $current_product_id . '_' . $child_key . '_errors' , $double_exclusive ) ) {
								$double_exclusive[ 'af_cp_component_product_' . $current_product_id . '_' . $child_key . '_errors' ] = '';
							}
						}
						$stock_array = af_cp_product_stock( $product_id );

						if ( $product_obj->is_type('variable') ) {

							if ( ( wc_get_product($variation_id_for_current) ) && ( 0 != $variation_id_for_current ) && ( '' != $variation_id_for_current ) ) {

								if ( 'parent' != ( wc_get_product($variation_id_for_current) )->managing_stock() ) {
									$stock_array = af_cp_product_stock( $variation_id_for_current );
								}
							} else {
								$check_double_product = true;
								continue;
							}
						}
						
						if ( 'yes' != $stock_array['type'] ) {

							if ( '' == $stock_array['count'] ) {
								ob_start();
								?>
								<p class="woocommerce-error">
									<?php echo esc_html__( get_the_title($product_id) . ' is out of stock' , 'af_comp_product'); ?>
								</p>
								<?php
								$double_exclusive[ 'af_cp_component_product_' . $current_product_id . '_' . $key . '_errors' ] = ob_get_clean();

								$check_double_product = true;

							} elseif ( ( (int) $qty * (int) $quantity_of_current_product ) > $stock_array['count'] ) {

								ob_start();
								?>
								<p class="woocommerce-error">
									<?php echo esc_html__( get_the_title($product_id) . ' have stock of (' . $stock_array['count'] . ') more can not selected' , 'af_comp_product'); ?>
								</p>
								<?php
								$double_exclusive[ 'af_cp_component_product_' . $current_product_id . '_' . $key . '_errors' ] = ob_get_clean();

								$check_double_product = true;
							}
						}
					} else {
						$selected_products[ 'af_cp_selected_product_' . $current_product_id . '_' . $key ] = '';

						if ( 'required' == af_cp_required_comp( $product_id , $key ) ) {

							ob_start();
							$comp_name_notice = $comp_name;

							if ( '' == $comp_name ) {
								$comp_name_notice = 'This component ';
							}

							?>
								<p class="woocommerce-error">
									<?php echo esc_html__(  $comp_name_notice . ' is required' , 'af_comp_product'); ?>
								</p>
								<?php

								$double_exclusive[ 'af_cp_component_product_' . $current_product_id . '_' . $key . '_errors' ] = ob_get_clean();
								$check_double_product = true;

						} else {

							$double_exclusive[ 'af_cp_component_product_' . $current_product_id . '_' . $key . '_errors' ] = '';
						}
					}
				}
			} else {
				$check_double_product = true;
			}

		//$calculated_regular_price = af_cp_calculate_general_price( $current_product_id , $total_regular_price );
		 $adjust_type_price_show  = get_post_meta( $current_product_id , 'af_comp_product_price_adjustment' , true );

		 $calculated_regular_price = $total_regular_price;
		
		$calculated_sale_price = af_cp_calculate_general_price( $current_product_id , $total_sale_price );

			$price     = wc_price( $calculated_sale_price );
			$set_price = $calculated_sale_price;

			
			if ( ( 'fixed_increase'==$adjust_type_price_show )||( 'percentage_increase'==$adjust_type_price_show )) {
			   $price = '<ins>' . wc_price( $calculated_sale_price ) . '</ins>' ;
			} else if ( $calculated_regular_price > $calculated_sale_price ) {
			   $price = '<del>' . wc_price( $calculated_regular_price ) . '</del> <ins>' . wc_price( $calculated_sale_price ) . '</ins>' ;
			}

			if ( $check_double_product ) {
				wp_send_json(
					array(
						'success'          => 'no',
						'validation_msg'   => $double_exclusive, 
						'selected_product' => $selected_products, 
						'price'            => $price, 
						'set_price'        => $set_price, 
					)
					);
					die();
			}

			wp_send_json(
				array(
					'success'          => 'yes',
					'validation_msg'   => $double_exclusive, 
					'selected_product' => $selected_products, 
					'price'            => $price, 
					'set_price'        => $set_price, 
				)
				);
			die();
		}

		public function af_cp_thumbnail_pagination_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$form_table = array();
			if (isset($_POST['form_data'])) {
				parse_str( sanitize_meta( '', $_POST['form_data'], '' ), $form_data );
				$form_table = sanitize_meta( '', wp_unslash( $form_data ), '' );
			}



			$product_id      =  sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');
			$comp_key        =  sanitize_text_field( isset($_POST['comp_key']) ? $_POST['comp_key'] : '');
			$order           =  sanitize_text_field( isset($_POST['order_key']) ? $_POST['order_key'] : '');
			$type            =  sanitize_text_field( isset($_POST['type_key']) ? $_POST['type_key'] : '');
			$next_page       =  sanitize_text_field( isset($_POST['next_page']) ? $_POST['next_page'] : '');
			$current_page    =  sanitize_text_field( isset($_POST['current_page']) ? $_POST['current_page'] : '');
			$default_product =  sanitize_text_field( isset($_POST['selected']) ? $_POST['selected'] : '');

			if ( str_contains( strtolower($next_page) , 'previous' ) ) {
				$next_page = floatval($current_page) - 1;
			} elseif ( str_contains( strtolower($next_page) , 'next' ) ) {
				$next_page = floatval($current_page) + 1;
			}

			$scenario_validate = (array) af_cp_validate_scenario_fn( $product_id , $form_table );
			$all_comp_products = (array) $scenario_validate['options'];
			
			$filtered_products = array();

			if ( isset($all_comp_products[ $comp_key ]) ) {
				$filtered_products = $all_comp_products[ $comp_key ];
			}

			$af_cp_form_location       = get_post_meta( $product_id , 'af_cp_form_location' , true );
				$list_style            = 'simple';
				$af_cp_component_style = (array) get_post_meta( $product_id , 'af_composite_product_component_style' , true );

			if ( isset($af_cp_component_style[ $comp_key ]) ) {
				$list_style = $af_cp_component_style[ $comp_key ];
			}

				$af_cp_quantity_op = (array) get_post_meta( $product_id , 'af_composite_product_quantity_op' , true );
				$qty_option        = '';

			if ( isset($af_cp_quantity_op[ $comp_key ]) ) {
				$qty_option = $af_cp_quantity_op[ $comp_key ];
			}

				$fixed_qty_text = '';
				$fixed_qty      = 1;

			if ( 'fixed' == $qty_option ) {
				$fixed_qty_op = (array) get_post_meta( $product_id , 'af_composite_product_fixed_qty' , true );

				if ( isset($fixed_qty_op[ $comp_key ]) ) {
					$fixed_qty = $fixed_qty_op[ $comp_key ];
				}

				if ( '' == $fixed_qty ) {
					$fixed_qty = 1;
				}

				$fixed_qty_text = $fixed_qty . ' &times; ';
			}
						
			ob_start();

			if ( 'thumbnail' == $list_style ) {
				af_cp_thumbnail_options( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option , $next_page );

			}

			//  af_cp_component_common_qty( $product_id , $qty_option , $comp_key , $fixed_qty );

			$return_replace = ob_get_clean();

			wp_send_json( $return_replace );
			
			die();
		}

		public function af_cp_sort_selection_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$form_table = array();

			if (isset($_POST['form_data'])) {
				parse_str( sanitize_meta( '', $_POST['form_data'], '' ), $form_data );
				$form_table = sanitize_meta( '', wp_unslash( $form_data ), '' );
			}

			$product_id =  sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');
			$comp_key   =  sanitize_text_field( isset($_POST['comp_key']) ? $_POST['comp_key'] : '');
			$order      =  sanitize_text_field( isset($_POST['order_key']) ? $_POST['order_key'] : '');
			$type       =  sanitize_text_field( isset($_POST['type_key']) ? $_POST['type_key'] : '');

			$scenario_validate = (array) af_cp_validate_scenario_fn( $product_id , $form_table );
			$all_comp_products = $scenario_validate['options'];

			$filtered_products = array();

			if ( isset($all_comp_products[ $comp_key ]) ) {
				$filtered_products = $all_comp_products[ $comp_key ];
			}

			$list_style            = 'simple';
			$af_cp_component_style = (array) get_post_meta( $product_id , 'af_composite_product_component_style' , true );

			if ( isset($af_cp_component_style[ $comp_key ]) ) {
				$list_style = $af_cp_component_style[ $comp_key ];
			}

			$af_cp_quantity_op = (array) get_post_meta( $product_id , 'af_composite_product_quantity_op' , true );
			$qty_option        = '';

			if ( isset($af_cp_quantity_op[ $comp_key ]) ) {
				$qty_option = $af_cp_quantity_op[ $comp_key ];
			}
			$fixed_qty_text = '';
			$fixed_qty      = 1;

			if ( 'fixed' == $qty_option ) {
				$fixed_qty_op = (array) get_post_meta( $product_id , 'af_composite_product_fixed_qty' , true );

				if ( isset($fixed_qty_op[ $comp_key ]) ) {
					$fixed_qty = $fixed_qty_op[ $comp_key ];
				}

				if ( '' == $fixed_qty ) {
					$fixed_qty = 1;
				}

				$fixed_qty_text = $fixed_qty . ' &times; ';
			}
			$args                   = array();
			$args['posts_per_page'] = -1;
			$args['post_type']      = array( 'product', 'product_variation' );
			$args['post__in']       = $filtered_products;
			$args['post_status']    = 'publish';
			$args['fields']         = 'ids';

			if ( ( 'asc' == $order ) || ( 'desc' == $order ) ) {
				$args['order'] = $order;
			}

			if ( ( 'price' != $type ) && ( 'name' != $type ) && ( 'date' != $type ) && ( 'rating' != $type ) && ( 'popularity' != $type ) ) {
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_price';
			} elseif ( 'price' == $type ) {

					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_price';
			} elseif ( 'rating' == $type ) {
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_wc_average_rating';
			} elseif ( 'name' == $type ) {
				$args['orderby'] = 'title';
			} elseif ( 'date' == $type ) {
				$args['orderby'] = 'date';
			} elseif ( 'popularity' == $type ) {
				$args['orderby'] = 'popularity';
			}

			if ( 'thumbnail' == $list_style ) {
				$af_cp_form_location = get_post_meta( $product_id , 'af_cp_form_location' , true );

				if ( 'after_desc' == $af_cp_form_location ) {
					$args['posts_per_page'] = 3;
				} else {
					$args['posts_per_page'] = 9;
				}
				$args['paged'] = 1;
			}

			$filtered_products = get_posts($args);


			
			$default_product = af_cp_component_default_product( $product_id , $comp_key );

			if ( !in_array( $default_product , (array) $filtered_products ) ) {
				$default_product = current($filtered_products);
			}
						
			ob_start();

			if ( 'simple' == $list_style ) {
				af_cp_simple_dropdown( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option );

			} elseif ( 'image_product' == $list_style ) {
				af_cp_image_dropdown( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option );

			} elseif ( 'radio' == $list_style ) {
				af_cp_radio_options( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option );

			} elseif ( 'thumbnail' == $list_style ) {
				af_cp_thumbnail_options( $product_id , $comp_key , $default_product , $filtered_products , $fixed_qty_text , $fixed_qty , $qty_option , 1 );

			}
				//af_cp_component_common_qty( $product_id , $qty_option , $comp_key , $fixed_qty );
			$return_replace = ob_get_clean();

			wp_send_json(
				array(
					'success' => 'yes',
					'data'    => $return_replace,
				)
				);
			die();
		}

		public function af_cp_add_scenario_actions_ajax_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$condition_key =  sanitize_text_field( isset($_POST['size']) ? $_POST['size'] : '');
			$product_id    =  sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');
			$key           =  sanitize_text_field( isset($_POST['key']) ? $_POST['key'] : '');
			$selected      = sanitize_text_field( isset($_POST['selected']) ? $_POST['selected'] : '');
			$scenario_ajax = 'ajax';

			ob_start();
				include AFCPB_DIR_PATH . '/includes/product-settings/scenario/af-action.php';
			$row = ob_get_clean(); 

				wp_send_json(
				array(
					'success' => 'yes',
					'size'    => floatval($condition_key) + 1,
					'tr_data' => $row, 
				)
				);
			die();
		}

		public function af_cp_add_component_products_ajax_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$product_id         =  sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');
			$selected           =  sanitize_text_field( isset($_POST['selected']) ? $_POST['selected'] : '');
			$products           = array();
			$categories         = array();
			$tags               = array();
			$component_all_prod = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );
			$component_cats     = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
			$component_tags     = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );

			if ( array_key_exists( $selected , $component_all_prod ) ) {
				$products = (array) $component_all_prod[ $selected ];
			} else {
				$products = array();
			}

			if ( array_key_exists( $selected , $component_cats ) ) {
				$categories = (array) $component_cats[ $selected ];
			} else {
				$categories = array();
			}

			if ( array_key_exists( $selected , $component_tags ) ) {
				$tags = $component_tags;
			} else {
				$tags = array();
			}

			$return_array = '';
			$all_products = merge_all_products( $products , $categories , $tags );

			foreach ( $all_products as $key => $value) {
				ob_start();
				?>
				<option value="<?php echo esc_attr($value); ?>"><?php echo esc_html__( get_the_title($value) , 'af_comp_product' ); ?></option>
				<?php
				$return_array .= ob_get_clean(); 
			}

			wp_send_json( $return_array );
			die();
		}

		public function af_composite_product_add_component_ajax_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}
			
			$key = sanitize_text_field( isset($_POST['array_size']) ? $_POST['array_size'] : '');

			$af_comp_product_component_name    = 'New Component ' . $key;
			$af_comp_product_component_desc    = '';
			$af_comp_product_component_image   = '';
			$af_composite_product_all_products = array();
			$af_composite_product_categories   = array();

			$af_composite_product_tags               = array();
			$af_composite_product_default_product    = '';
			$af_composite_product_required_component = '';
			$af_composite_product_sorting_cb         = '';

			$af_cp_selection_title_cb     = 'yes';
			$af_cp_selection_desc_cb      = 'yes';
			$af_cp_selection_thumbnail_cb = 'yes';
			$af_cp_selection_price_cb     = 'yes';

			$af_cp_selection_stock_cb         = 'yes';
			$af_composite_product_quantity_op = '';
			$af_composite_product_fixed_qty   = 1;
			$af_composite_product_min_qty     = 1;

			$af_composite_product_max_qty         = '';
			$af_composite_product_component_style = '';
			$af_cp_comp_adj_price_value           = 0;
			$af_cp_comp_adj_type                  = '';

			$af_composite_product_price_style  = '';
			$af_composite_product_stock_cb     = '';
			$af_composite_product_order        = '';
			$af_composite_product_type         = '';
			$af_composite_product_exclusive_cb = '';

			ob_start();
				include AFCPB_DIR_PATH . '/includes/product-settings/component/af-single-component.php';
			$row = ob_get_clean(); 

				wp_send_json(
				array(
					'success' => 'yes',
					'tr_data' => $row, 
				)
				);
			die();
		}

		public function af_cp_add_actions_scenario_ajax_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$key                           = sanitize_text_field( isset($_POST['array_size']) ? $_POST['array_size'] : '');
			$product_id                    = sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');
			$af_comp_product_scenario_name = 'New Scenario ' . $key;

			ob_start();
			$af_cp_enable_scenario_cb           = 'yes';
			$af_comp_product_scenario_desc      = '';
			$af_comp_product_sc_hide_comp_cb    = '';
			$af_comp_product_sc_hide_options_cb = '';
			$af_cp_sc_hide_comps                = array();

				include AFCPB_DIR_PATH . '/includes/product-settings/scenario/af-single-scenario.php';
			$row = ob_get_clean(); 
				wp_send_json(
				array(
					'success' => 'yes',
					'tr_data' => $row, 
				)
				);
			die();
		}

		public function af_onload_composite_product_dropdown_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			if (isset($_POST['product_id'])) {
				$product_id = sanitize_text_field( $_POST['product_id'] );
			} else {
				$product_id = 0;
			}

			if (isset($_POST['key_id'])) {
				$key_id =  sanitize_text_field( $_POST['key_id'] );
			} else {
				$key_id = 0;
			}

			$return_array   = array();
			$remove_options = 'yes';

			if ( 'ajax_onload' != get_option('af_min_no_product_for_ajax_op') ) {
				$remove_options = 'no';
			}

			$component_all_prod = (array) get_post_meta( $product_id , 'af_composite_product_all_products' , true );
			$component_cats     = (array) get_post_meta( $product_id , 'af_composite_product_categories' , true );
			$component_tags     = (array) get_post_meta( $product_id , 'af_composite_product_tags' , true );
			
			$products   = array();
			$categories = array();
			$tags       = array();

			if ( isset($component_all_prod[ $key_id ]) ) {
				$products = (array) $component_all_prod[ $key_id ];
			} else {
				$products = array();
			}

			if ( isset($component_cats[ $key_id ]) ) {
				$categories = (array) $component_cats[ $key_id ];
			} else {
				$categories = array();
			}

			if ( isset($component_tags[ $key_id ]) ) {
				$tags = (array) $component_tags[ $key_id ];
			} else {
				$tags = array();
			}

			$all_products = merge_all_products( $products , $categories , $tags );
			
			$quantity_op                      = (array) get_post_meta( $product_id , 'af_composite_product_quantity_op' , true );
			$af_composite_product_quantity_op = 'fixed';

			if ( array_key_exists( $key_id , $quantity_op ) ) {
				$af_composite_product_quantity_op = $quantity_op[ $key_id ];
			}

			$prod_quantity                  = (array) get_post_meta( $product_id , 'af_composite_product_fixed_qty' , true );
			$af_composite_product_fixed_qty = 1;

			if ( array_key_exists( $key_id , $prod_quantity ) ) {
				$af_composite_product_fixed_qty = $prod_quantity[ $key_id ];
			}

			$component_price_style = (array) get_post_meta( $product_id , 'af_composite_product_price_style' , true );
			$price_style           = '';

			if ( array_key_exists( $key_id , $component_price_style ) ) {
				$price_style = $component_price_style[ $key_id ];
			}
			
			$component_stock_cb = (array) get_post_meta( $product_id , 'af_composite_product_stock_cb' , true );
			$stock_style        = '';

			if ( array_key_exists( $key_id , $component_stock_cb ) ) {
				$stock_style = $component_stock_cb[ $key_id ];
			}

			$no_of_products_to_show = abs( get_option('af_min_no_of_product_for_ajax') );
			
			$no_of_products_to_show_qty = 0;

			foreach ($all_products as $key => $loop_product_id) {
				$product = wc_get_product($loop_product_id);

				if ( !$product ) {
					continue;
				}

				if ( array_key_exists( $loop_product_id , $return_array ) ) {
					continue;
				}
				
				$value_text = '';

				if ( 'fixed' == $af_composite_product_quantity_op ) {
					$value_text .= $af_composite_product_fixed_qty . ' X ';
				}

				$value_text .= esc_html__( get_the_title($loop_product_id) , 'af_comp_product' );
				$price       = '';

				if ( 'full_price' == $price_style ) {
					$price = $product->get_price_html();
				} elseif ( '' != $product->get_price() ) {

						$price =  wc_price( $product->get_price() );
				}

				if ( '' != $price ) {
					$value_text .=  ' ( ' . $price . ' ) ';
				}

				if ( ( 'disable' != $stock_style ) || ( 'both' == $stock_style ) ) { 
					$value_text .=  ' - ' . $this->get_stock_msg_with_qty($loop_product_id);
				} 

				if ( $no_of_products_to_show_qty >= $no_of_products_to_show ) {
					break;
				}
					++$no_of_products_to_show_qty;

				$return_array[] = '<option value="' . $loop_product_id . '"> ' . $value_text . '</option>';

			}

			wp_send_json( 
				array(
					'remove_options' => $remove_options,
					'data'           => $return_array,
				)
			);
		}

		public function af_cp_add_scenario_condition_ajax_cb() {
			
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce' ) ) {
				die( esc_html__( 'Failed Ajax security check!', 'af_comp_product' ) );
			}

			$condition_key =  sanitize_text_field( isset($_POST['size']) ? $_POST['size'] : '');
			$product_id    =  sanitize_text_field( isset($_POST['product_id']) ? $_POST['product_id'] : '');
			$key           =  sanitize_text_field( isset($_POST['key']) ? $_POST['key'] : '');
			$selected      = sanitize_text_field( isset($_POST['selected']) ? $_POST['selected'] : '');
			$scenario_ajax = 'ajax';

			ob_start();
				include AFCPB_DIR_PATH . '/includes/product-settings/scenario/af-condition.php';
			$row = ob_get_clean(); 

				wp_send_json(
				array(
					'success' => 'yes',
					'size'    => floatval($condition_key) + 1,
					'tr_data' => $row, 
				)
				);
			die();
		}

		public function check_qty_of_product_in_cart( $product_id ) {
			$cart     = WC()->cart->get_cart();
			$quantity = 0;

			foreach ($cart as $key => $value) {

				if ( $product_id == $value['data']->get_id() ) {
					$quantity += $value['quantity'];
				}
			}

			return $quantity;
		}

		public function af_apply_price_adjustments( $current_product_id, $product_price_def ) {
			$af_comp_product_price_adjustment = get_post_meta( $current_product_id , 'af_comp_product_price_adjustment' , true );
			$af_comp_product_adj_value        = get_post_meta( $current_product_id , 'af_comp_product_adj_value' , true );

			if ( '' != $af_comp_product_adj_value ) {
				$percentage_price = ( $product_price_def /100 ) * $af_comp_product_adj_value;
			}

			if ( 'fixed_price' == $af_comp_product_price_adjustment ) {

				if ( '' != $af_comp_product_adj_value ) {
					$product_price_def = $af_comp_product_adj_value;
				}
			} elseif ( 'fixed_increase' == $af_comp_product_price_adjustment ) {

				if ( '' != $af_comp_product_adj_value ) {
					$product_price_def += $af_comp_product_adj_value;
				}
			} elseif ( 'fixed_decrease' == $af_comp_product_price_adjustment ) {

				if ( '' != $af_comp_product_adj_value ) {
					$product_price_def -= $af_comp_product_adj_value;
				}

				if ( $product_price_def <0 ) {
					$product_price_def = 0;
				}
			} elseif ( 'percentage_increase' == $af_comp_product_price_adjustment ) {

				if ( '' != $af_comp_product_adj_value ) {
					$product_price_def += $percentage_price;
				}

			} elseif ( 'percentage_decrease' == $af_comp_product_price_adjustment ) {

				if ( '' != $af_comp_product_adj_value ) {
					$product_price_def -= $percentage_price;
				}

				if ( $product_price_def <0 ) {
					$product_price_def = 0;
				}

			}

			return $product_price_def;
		}

		public function get_stock_msg_with_qty( $product_id ) {
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
	new ADF_Composite_Product_Ajax_Fns();
}
