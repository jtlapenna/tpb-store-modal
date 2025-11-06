<?php
/**
 * Packed packages meta data interpreter.
 *
 * @package WPDesk\WooCommerceShipping\Ups
 */

namespace WPDesk\FlexibleShippingUpsPro\Legacy;

use UpsProVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreter;

/**
 * Can interpret previous version packed packages meta data from WooCommerce order shipping on admin.
 *
 * @deprecated Used only for displaying meta data created by plugin version older than 1.3.
 */
class PackedPackagesAdminMetaDataInterpreter implements SingleAdminOrderMetaDataInterpreter {

	const META_KEY = 'ups_packed_packages';

	/**
	 * Get meta key on admin order edit page.
	 *
	 * @param string         $display_key .
	 * @param \WC_Meta_Data  $meta .
	 * @param \WC_Order_Item $order_item .
	 *
	 * @return string
	 */
	public function get_display_key( $display_key, $meta, $order_item ) {
		return __( 'Packages', 'flexible-shipping-ups-pro' );
	}

	/**
	 * Get meta value on admin order edit page.
	 *
	 * @param string         $display_value .
	 * @param \WC_Meta_Data  $meta .
	 * @param \WC_Order_Item $order_item .
	 *
	 * @return string
	 */
	public function get_display_value( $display_value, $meta, $order_item ) {
		$data          = $meta->get_data();
		$display_value = '';
		$packages      = json_decode( $data['value'], true );
		foreach ( $packages as $package ) {
			if ( ! empty( $display_value ) ) {
				$display_value .= '<br/>';
			}
			$display_value .= ' <strong>' . $package['package'] . ':</strong> ';
			foreach ( $package['items'] as $item ) {
				$display_value .= $item['name'] . ' x ' . $item['quantity'] . ', ';
			}
			$display_value = trim( trim( $display_value ), ',' );
		}

		return $display_value;
	}


	/**
	 * Is supported key on admin?
	 *
	 * @param string $display_key .
	 *
	 * @return bool
	 */
	public function is_supported_key_on_admin( $display_key ) {
		return self::META_KEY === $display_key;
	}

}
