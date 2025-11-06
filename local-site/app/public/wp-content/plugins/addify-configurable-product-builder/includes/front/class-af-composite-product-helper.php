<?php

/**
 * Helper class that provides necessary hooks for plugin accessibility and compatibility.
 * This class facilitates integration with other WordPress plugins through action and filter hooks.
 */
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'Af_Composite_Product_Helper' ) ) {
	class Af_Composite_Product_Helper {

		public function __construct() {
		}
	   
		/**
		 * Handles add to cart validation for price calculator products
		 *
		 * @param bool   $passed            Default validation status
		 * @param int    $product_id        Product ID being added to cart
		 * @param int    $quantity          Quantity being added
		 * @param int    $variation_id      Variation ID if product is a variation
		 * @param array  $variation         Variation data
		 * @param array  $cart_item_data    Extra cart item data
		 * @return WP_Error|bool  True if validation passes, wp_error otherwise
		 */
		public static function afcpb_add_to_cart_validator( $passed, $product_id, $quantity, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {

			$product = wc_get_product($product_id);

			if ( 'af_composite_product' != $product->get_type() ) {
				return $passed;
			}

			$nonce = isset( $_POST['afcpb_files_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['afcpb_files_nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce_action' ) ) {
				die( esc_html__( 'Failed security check!', 'af_comp_product' ) );
			}

			$af_comp_product_adj_min_qty = get_post_meta( $product_id , 'af_comp_product_adj_min_qty' , true );
			$af_comp_product_adj_max_qty = get_post_meta( $product_id , 'af_comp_product_adj_max_qty' , true );
			$quantity                    = sanitize_text_field( isset($_POST['quantity']) ? $_POST['quantity'] : 1 );
			
			if ( ( '' != $af_comp_product_adj_max_qty ) && ( '' != $af_comp_product_adj_max_qty )  ) {
				if ( ( $quantity < $af_comp_product_adj_min_qty ) || ( $quantity > $af_comp_product_adj_max_qty ) ) {
					return new WP_Error('invalid_quantity', sprintf(__( ' Quantity of ' . get_the_title($product_id) . ' must be between %s and %s', 'woocommerce' ), $af_comp_product_adj_min_qty , $af_comp_product_adj_max_qty));
				}
			} elseif ( ( '' != $af_comp_product_adj_max_qty ) && ( '' == $af_comp_product_adj_max_qty ) ) {
				if ( $quantity < $af_comp_product_adj_min_qty ) {
					return new WP_Error('invalid_quantity', sprintf(__( ' Quantity of ' . get_the_title($product_id) . ' must be greater than %s', 'woocommerce' ), $af_comp_product_adj_min_qty));
				}
			} elseif ( ( '' == $af_comp_product_adj_max_qty ) && ( '' != $af_comp_product_adj_max_qty )  ) {
				if ( $quantity > $af_comp_product_adj_max_qty ) {
					return new WP_Error('invalid_quantity', sprintf(__( ' Quantity of ' . get_the_title($product_id) . ' must be less than %s', 'woocommerce' ), $af_comp_product_adj_max_qty));
				}
			}


			// need $qty calculated to be sent from rfq
			// $cart_object    = wc()->cart->get_cart();
			$quote_object    = (array) wc()->session->get('quotes'); 
			$qty_calculated = 0;

			foreach ( $quote_object as $quote_value ) {
				if ( $quote_value['data']->get_id() == $product_id ) {
					$qty_calculated += $quote_value['quantity'];
				}
			}

			if ( '' != $af_comp_product_adj_max_qty ) {

				if ( $qty_calculated > $af_comp_product_adj_max_qty ) {
					return new WP_Error('invalid_quantity', sprintf(__( get_the_title($product_id) . ' is already in quote more can not be added '  , 'woocommerce')));
				}

				if ( ( $qty_calculated + $quantity )> $af_comp_product_adj_max_qty  ) {
					$remaining_text = '';
					if ( ( $af_comp_product_adj_max_qty - $qty_calculated ) > 0 ) {
						$remaining_text = ' only ' . ( (int) $af_comp_product_adj_max_qty - (int) $qty_calculated ) . ' more can be added';
					}
					return new WP_Error('invalid_quantity', sprintf(__( $qty_calculated . ' ' . get_the_title($product_id) . ' are already in quote ' . $remaining_text , 'woocommerce')));
				}
			}
			// ends here code


			$all_component_products = array();
			if ( isset($_POST['af_cp_component_product']) ) {
				$all_component_products = (array) sanitize_meta('', $_POST['af_cp_component_product'] , '');
			}
			$all_cp_variation_ids = array();

			if ( isset($_POST['af_cp_variation_id']) ) {
				$all_cp_variation_ids = (array) sanitize_meta('', $_POST['af_cp_variation_id'] , '' );
			}
			$all_qty = array();

			if ( isset($_POST['af_cp_component_product_qty']) ) {
				$all_qty = (array) sanitize_meta('', $_POST['af_cp_component_product_qty'] , '' );
			}

			$scenario_validation     = (array) af_cp_validate_scenario_fn( $product_id , $_POST );
			$scenario_filer_products = (array) $scenario_validation['options'];
			$scenario_hidden_comp    = (array) $scenario_validation['hide'];

			$errors = new WP_Error();

			foreach ( $all_component_products as $key => $value) {

				if ( in_array( $key , $scenario_hidden_comp ) ) {
					unset($all_component_products[ $key ]);
				}

				if ( wc_get_product($value) ) {
					
					if ( isset( $scenario_filer_products[ $key ] ) ) {

						if ( !in_array( $value , (array) $scenario_filer_products[ $key ] ) ) {

							// translators: %s is the product name
							$errors->add('invalid_selection_' . $value, sprintf( __( '%s cannot be selected', 'woocommerce' ), get_the_title($value) ));
							$passed = false;
						}
					}
				}
			}

			if ( $errors->has_errors() ) {
				return $errors;
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
			
			$quote                   = (array) wc()->session->get('quotes');
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

						$errors->add(
							'invalid_selection_' . $value,
							sprintf( __( '%s is selected in exclusive component ' . $component_title . ' . Please choose another product', 'woocommerce' ), get_the_title($value) )
						);
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

						foreach ( $quote as $quote_value ) {

							if ( $prod_id == $quote_value['data']->get_id() ) {
								$prod_quantity += $quote_value['quantity'];
							}
						}

						if ('af_composite_product' != $product->get_type()) {
							$total_qty          = $prod_quantity * $quantity;
						} else {
							$total_qty          = $quantity;
						}
						
						// $total_qty          = $prod_quantity * $quantity;
						$product_have_stock = (array) af_cp_product_stock( $product_id );

						
						if ( 'yes' != $product_have_stock['type'] ) {

							if ( '' == $product_have_stock['count'] ) {
								// translators: %s is the product name
								$errors->add('invalid_selection_' . $value, sprintf( __( '%s is out of stock', 'woocommerce' ), get_the_title($value) ));
								// wc_add_notice(__( get_the_title($product_id) . ' is out of stock' , 'woocommerce'), 'error');
								$passed = false;
							} elseif ( $total_qty > $product_have_stock['count'] ) {
								// translator: %s is the product name
								$errors->add('invalid_selection_' . $value, sprintf( __( '%s have stock98 of (' . $product_have_stock['count'] . ') more can not selected', 'woocommerce' ), get_the_title($value) ));
								$passed = false;
							}
						}
					}
				}
			}
			if ( $errors->has_errors() ) {    
				return $errors;
			}
			return $passed;
		}

		public static function afcpb_add_cart_item_meta( $cart_item_data, $product_id = '', $variation_id = '' ) {


			if (isset($_POST['af_cp_component_product'])) {

				$nonce = isset( $_POST['afcpb_files_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['afcpb_files_nonce'] ) ) : 0;

				if ( ! wp_verify_nonce( $nonce, 'afcpb_files_nonce_action' ) ) {
					die( esc_html__( 'Failed security check!', 'af_comp_product' ) );
				}

				$cart_item_data['af_cp_component_product'] = (array) sanitize_meta('', $_POST['af_cp_component_product'] , '' );
				$af_cp_component_product                   = (array) sanitize_meta('', $_POST['af_cp_component_product'] , '' );

				foreach ( (array) $af_cp_component_product as $key => $value) {
					$product = wc_get_product($value);

					if ( !$product ) {
						continue;
					}

					if ( $product->is_type('variable') ) {
						$attributes = $product->get_variation_attributes();

						foreach ( $attributes as $child_key => $child_value ) {
							$child_attr_key = strtolower('attribute_' . $child_key);

							if ( isset( $_POST[ $child_attr_key ] ) ) {
								$cart_item_data[ $child_attr_key ] = (array) sanitize_meta('', $_POST[ $child_attr_key ] , '' );
							}
						}
					}
				}
			}

			if (isset($_POST['af_cp_variation_id'])) {
				$cart_item_data['af_cp_variation_id'] = (array) sanitize_meta('', $_POST['af_cp_variation_id'] , '' ) ;
			}

			if (isset($_POST['af_cp_component_product_qty'])) {
				$cart_item_data['af_cp_component_product_qty'] = (array) sanitize_meta('', $_POST['af_cp_component_product_qty'] , '') ;
			}

			return $cart_item_data;
		}
	}
}
