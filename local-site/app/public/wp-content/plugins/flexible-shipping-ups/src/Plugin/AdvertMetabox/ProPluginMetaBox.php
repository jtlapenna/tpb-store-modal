<?php

namespace WPDesk\FlexibleShippingUps\AdvertMetabox;


use UpsFreeVendor\Octolize\Brand\Assets\AdminAssets;
use UpsFreeVendor\Octolize\Brand\UpsellingBox\SettingsSidebar;
use UpsFreeVendor\Octolize\Brand\UpsellingBox\ShippingMethodInstanceShouldShowStrategy;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use UpsFreeVendor\WPDesk\ShowDecision\OrStrategy;
use UpsFreeVendor\WPDesk\ShowDecision\WooCommerce\ShippingMethodStrategy;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsShippingService;

class ProPluginMetaBox implements Hookable, HookableCollection {

	use HookableParent;

	private string $assets_url;

	public function __construct( string $assets_url ) {
		$this->assets_url = $assets_url;
	}

	public function hooks() {
		add_action( 'flexible_shipping_ups_settings_sidebar', [ $this, 'enqueue_scripts' ] );

		$should_show_strategy = new OrStrategy( new ShippingMethodStrategy( UpsShippingService::UNIQUE_ID ) );
		$should_show_strategy->addCondition( new ShippingMethodInstanceShouldShowStrategy( new \WC_Shipping_Zones(), UpsShippingService::UNIQUE_ID ) );
		$this->add_hookable( new AdminAssets( $this->assets_url, 'ups', $should_show_strategy ) );
		$settings_sidebar = new SettingsSidebar(
			'flexible_shipping_ups_settings_sidebar',
			$should_show_strategy,
			__( 'Get UPS Live Rates PRO!', 'flexible-shipping-ups' ),
			[
				__( 'Handling Fees', 'flexible-shipping-ups' ),
				__( 'Delivery dates', 'flexible-shipping-ups' ),
				__( 'Box packing', 'flexible-shipping-ups' ),
				__( 'Access Points Select', 'flexible-shipping-ups' ),
				__( 'Flat Rate for Access Points', 'flexible-shipping-ups' ),
				__( 'Multicurrency support', 'flexible-shipping-ups' ),
				__( 'Simple Rate support', 'flexible-shipping-ups' ),
			],
			'pl_PL' === get_locale() ? 'https://octol.io/ups-upgrade-box-pl' : 'https://octol.io/ups-upgrade-box',
			__( 'Upgrade Now', 'flexible-shipping-ups' ),
			1200,
			20,
			'#mainform h2:first,#mainform h3:first'
		);
		if ( $this->show_pro_features() ) {
			$settings_sidebar->set_additional_content( $this->get_additional_content() );
		}
		$this->add_hookable( $settings_sidebar );

		$this->hooks_on_hookable_objects();
	}

	private function get_additional_content(): string {
		ob_start();
		include __DIR__ . '/view/html-pro-features.php';

		return ob_get_clean();
	}

	public function enqueue_scripts(): void {
		if ( ! defined( 'FLEXIBLE_SHIPPING_UPS_PRO_VERSION' ) ) {
			if ( $this->show_pro_features() ) {
				wp_enqueue_style( UpsShippingService::UNIQUE_ID );
				wp_enqueue_script( UpsShippingService::UNIQUE_ID );
			}
		}
	}


	/**
	 * @return bool
	 */
	private function show_pro_features(): bool {
		$page        = filter_input( INPUT_GET, 'page' );
		$tab         = filter_input( INPUT_GET, 'tab' );
		$instance_id = filter_input( INPUT_GET, 'instance_id' );

		return $page === 'wc-settings' && $tab === 'shipping' && $instance_id;
	}


}
