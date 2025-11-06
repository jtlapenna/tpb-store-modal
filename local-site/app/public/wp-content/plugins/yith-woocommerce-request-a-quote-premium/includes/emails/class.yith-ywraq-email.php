<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWRAQ_Email' ) ) {
	/**
	 * Base class for the Request a Quote emails
	 */
	class YITH_YWRAQ_Email extends WC_Email {
		/**
		 * The quote request data
		 *
		 * @var array
		 */
		public $raq = null;

		/**
		 * The order for the email
		 *
		 * @var WC_Order
		 */
		public $order = null;

		/**
		 * Class constructor
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Get the current RAQ information
		 *
		 * @return array|null
		 */
		public function get_raq() {
			return $this->is_email_preview() ? $this->get_dummy_raq() : $this->raq;
		}

		/**
		 * Get the current RAQ information
		 *
		 * @return WC_Order|null
		 */
		public function get_order() {
			return $this->is_email_preview() ? $this->get_dummy_quote() : $this->order;
		}

		/**
		 * Check if it is previewing the email
		 *
		 * @return bool
		 */
		protected function is_email_preview() {
			return apply_filters( 'woocommerce_is_email_preview', false );
		}

		/**
		 * Get the dummy RAQ information
		 *
		 * @return array
		 */
		protected function get_dummy_raq() {
			$dummy_quote                   = $this->get_dummy_quote();
			$dummy_raq                     = array();
			$dummy_raq['order_id']         = $dummy_quote->get_id();
			$dummy_raq['content_type']     = 'order_items';
			$dummy_raq['raq_content']      = $dummy_quote->get_items();
			$dummy_raq['first_name']       = $this->get_dummy_raq_field( 'first_name', 'text', 'First Name', $dummy_quote->get_billing_first_name() );
			$dummy_raq['last_name']        = $this->get_dummy_raq_field( 'last_name', 'text', 'Last Name', $dummy_quote->get_billing_last_name() );
			$dummy_raq['email']            = $this->get_dummy_raq_field( 'email', 'text', 'Email', $dummy_quote->get_billing_email() );
			$dummy_raq['message']          = $this->get_dummy_raq_field( 'message', 'textarea', 'Message', 'This is a message from the customer' );
			$dummy_raq['user_name']        = $dummy_raq['first_name']['value'] . ' ' . $dummy_raq['last_name']['value'];
			$dummy_raq['user_email']       = $dummy_raq['email']['value'];
			$dummy_raq['customer_message'] = $dummy_raq['message']['value'];
			$dummy_raq['order-number']     = $dummy_quote->get_id();
			$dummy_raq['order-id']         = $dummy_raq['order-number'];
			$dummy_raq['lang']             = $dummy_quote->get_meta( 'wpml_language' );
			$dummy_raq['expiration_data']  = $dummy_quote->get_meta( '_ywcm_request_expire' );
			return $dummy_raq;
		}

		/**
		 * Field generator for the Request a Quote data
		 *
		 * @param string $id ID for the field.
		 * @param string $type Type of the field.
		 * @param string $label Label for the field.
		 * @param string $value Value for the field.
		 * @return array
		 */
		protected function get_dummy_raq_field( $id, $type = 'text', $label = '', $value = '' ) {
			return array(
				'id'    => $id,
				'type'  => $type,
				'label' => $label,
				'value' => $value,
			);
		}

		/**
		 * Get the dummy quote for preview
		 *
		 * @return WC_Order
		 */
		protected function get_dummy_quote() {
			$dummy_quote = new WC_Order();
			$dummy_address = $this->get_dummy_address();
			// Add dummy product.
			$dummy_quote->add_product( $this->get_dummy_product() );
			// Add quote expiration if it's enabled in the plugin settings.
			if ( 'yes' === get_option( 'ywraq_enable_expired_time', 'no' ) ) {
				$expire_option = get_option( 'ywraq_expired_time', array( 'days' => 10 ) );
				$dummy_quote->add_meta_data( '_ywcm_request_expire', date( 'Y-m-d', strtotime( '+' . $expire_option['days'] . ' days' ) ) ); // phpcs:ignore
				$dummy_quote->add_meta_data( '_ywraq_enable_expiry_date', 'yes' );
			}
			// Set remaining order data.
			$dummy_quote->add_meta_data( 'ywraq_customer_name', $dummy_address['first_name'] );
			$dummy_quote->add_meta_data( 'ywraq_customer_email', $dummy_address['email'] );
			$dummy_quote->set_date_created( time() );
			$dummy_quote->set_currency( 'USD' );
			$dummy_quote->set_total( 10 );
			$dummy_quote->set_billing_address( $dummy_address );
			$dummy_quote->set_id( '123' );
			return $dummy_quote;
		}

		/**
		 * Get the dummy product for the preview
		 *
		 * @return WC_Product
		 */
		public static function get_dummy_product() {
			$dummy_product = new WC_Product();
			$dummy_product->set_name( _x( 'Quote product', '[ADMIN] Email preview', 'yith-woocommerce-request-a-quote' ) );
			$dummy_product->set_price( 10 );
			$dummy_product->set_id( 0 );
			return $dummy_product;
		}

		/**
		 * Get the dummy address for the quote
		 *
		 * @return string[]
		 */
		protected function get_dummy_address() {
			return array(
				'first_name' => 'John',
				'last_name'  => 'Doe',
				'company'    => 'Company',
				'email'      => 'john@company.com',
				'phone'      => '555-555-5555',
				'address_1'  => '123 Fake Street',
				'city'       => 'Faketown',
				'postcode'   => '12345',
				'country'    => 'US',
				'state'      => 'CA',
			);
		}
	}
}
