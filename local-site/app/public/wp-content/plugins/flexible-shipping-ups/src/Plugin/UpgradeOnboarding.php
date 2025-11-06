<?php

namespace WPDesk\FlexibleShippingUps;

use UpsFreeVendor\Octolize\Onboarding\PluginUpgrade\MessageFactory\LiveRatesFsRulesTable;
use UpsFreeVendor\Octolize\Onboarding\PluginUpgrade\PluginUpgradeMessage;
use UpsFreeVendor\Octolize\Onboarding\PluginUpgrade\PluginUpgradeOnboardingFactory;
use UpsFreeVendor\WPDesk_Plugin_Info;

class UpgradeOnboarding {

	private WPDesk_Plugin_Info $plugin_info;

	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	public function init_upgrade_onboarding(): void {
		$upgrade_onboarding = new PluginUpgradeOnboardingFactory(
			$this->plugin_info->get_plugin_name(),
			$this->plugin_info->get_version(),
			$this->plugin_info->get_plugin_file_name()
		);
		$upgrade_onboarding->add_upgrade_message(
			( new LiveRatesFsRulesTable() )->create_message( '3.0.0', $this->plugin_info->get_plugin_url() )
		);
		$upgrade_onboarding->add_upgrade_message(
			new PluginUpgradeMessage(
				'3.2.0',
				trailingslashit( $this->plugin_info->get_plugin_url() ) . 'vendor_prefixed/octolize/wp-onboarding/assets/images/icon-conditional-logic.svg',
				__( 'New way to use REST API', 'flexible-shipping-ups' ),
				sprintf(
				// Translators: %1$s and %2$s are placeholders for the link to the documentation.
					__( 'We\'re excited to announce a new method for accessing real-time shipping rates through the UPS REST API. From now on, you can use your own UPS application to connect to API and retrieve accurate live rates. This feature requires additional configuration. For more information, please %1$svisit our documentation%2$s.', 'flexible-shipping-ups' ),
					'<a href="' . ( ( get_locale() === 'pl_PL' ) ? 'https://octol.io/ups-method-docs-pl' : 'https://octol.io/ups-docs-oauth-app' ) . '" target="_blank">',
					'</a>'
				),
				'',
				''
			)
		);

		$upgrade_onboarding->create_onboarding();
	}

}
