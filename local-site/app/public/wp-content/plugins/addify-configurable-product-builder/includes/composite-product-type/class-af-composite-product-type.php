<?php
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WC_Product_Af_Composite' ) ) {
	class WC_Product_Af_Composite extends WC_Product {
		protected $product_type;

		public function __construct( $product = 0 ) {
			parent::__construct( $product );            
			$this->product_type = 'af_composite_product';
			$this->supports[]   = 'ajax_add_to_cart';
		}

		public function add_to_cart_url() {

			$url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg(
				'added-to-cart',
				add_query_arg(
					array(
						'add-to-cart' => $this->get_id(),
					),
					( function_exists( 'is_feed' ) && is_feed() ) || ( function_exists( 'is_404' ) && is_404() ) ? $this->get_permalink() : ''
				)
			) : $this->get_permalink();

			return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
		}
	
		public function add_to_cart_text() {

			$text = $this->is_purchasable() && $this->is_in_stock() ? esc_html__( 'Add to cart', 'af_comp_product' ) : esc_html__( 'Read more', 'af_comp_product' );
	
			return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
		}
	}
	$composite_product = new WC_Product_Af_Composite();
}
