<?php
/**
 *
 * UPS Free plugin disabler.
 *
 * @package WPDesk\FreeDisabler
 */

namespace WPDesk\FreeDisabler;

use UpsProVendor\WPDesk\Notice\Notice;

/**
 * Can disable free plugin.
 *
 * @package WPDesk\FreeDisabler
 */
class UpsFreeDisabler {

	/**
	 * Disable FedEx free.
	 */
	public static function disable_free() {
		add_action(
			'wp_builder_plugin_class',
			static function ( $class ) {
				if ( is_a( $class, \WPDesk\FlexibleShippingUps\Plugin::class, true )
					|| is_a( $class, \Flexible_Shipping_UPS_Plugin::class, true )
				) {
					require_once __DIR__ . '/NullPlugin.php';
					self::show_notice();

					return NullPlugin::class;
				}

				return $class;
			}
		);
	}

	/**
	 * Ensure notice that Free is disabled.
	 */
	public static function show_notice() {
		add_action(
			'admin_notices',
			static function () {
				if ( class_exists( Notice::class ) ) {
					$action = 'deactivate';
					$plugin = 'flexible-shipping-ups/flexible-shipping-ups.php';
					$url    = sprintf( admin_url( 'plugins.php?action=' . $action . '&plugin=%s&plugin_status=all&paged=1&s' ), $plugin );
					$url    = wp_nonce_url( $url, $action . '-plugin_' . $plugin );
					new Notice(
						sprintf(
						// Translators: link.
							__( '"Flexible Shipping for UPS" plugin can be removed now since the PRO version took over its functionalities.%1$s%2$sClick here%3$s to deactivate "Flexible Shipping For UPS" plugin.', 'flexible-shipping-ups-pro' ),
							'<br/>',
							'<a href="' . $url . '">',
							'</a>'
						)
					);
				}
			}
		);
	}
}
