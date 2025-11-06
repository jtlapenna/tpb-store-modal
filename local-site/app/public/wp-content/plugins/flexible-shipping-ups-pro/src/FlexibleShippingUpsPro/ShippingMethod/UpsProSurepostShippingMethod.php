<?php
/**
 * Shipping method.
 *
 * @package WPDesk\FlexibleShippingUpsPro\ShippingMethod;
 */

namespace WPDesk\FlexibleShippingUpsPro\ShippingMethod;

use UpsProVendor\WPDesk\UpsProShippingService\InstanceCustomOrigin\CustomOriginSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\PickupType\HandlingFeesSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\WooCommerceShipping\CustomOrigin\InstanceCustomOriginFields;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasEstimatedDeliveryDates;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasHandlingFees;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasInstanceCustomOrigin;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits\HandlingFeesTrait;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\UpsSurepostShippingMethod;
use UpsProVendor\WPDesk\WooCommerceShippingPro\Packer\PackerFactory;
use UpsProVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings;
use UpsProVendor\WPDesk\WooCommerceShippingPro\ProShippingMethod\ProMethodFieldsFactory;
use UpsProVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder\WooCommerceShippingBuilder;
use WPDesk\FlexibleShippingUpsPro\PackerBox\BoxFactory;

/**
 * UPS Pro Surepost Shipping method.
 */
class UpsProSurepostShippingMethod extends UpsSurepostShippingMethod implements HasHandlingFees, HasEstimatedDeliveryDates, HasInstanceCustomOrigin {

	use HandlingFeesTrait;

	/**
	 * Get length unit.
	 *
	 * @return string
	 */
	private function get_units() {
		return $this->get_option( UpsSettingsDefinition::UNITS, UpsSettingsDefinition::UNITS_IMPERIAL );
	}

	/**
	 * Is unit metric?
	 *
	 * @return bool
	 */
	private function is_unit_metric() {
		return isset( $this->settings[ UpsSettingsDefinition::UNITS ] )
			? UpsSettingsDefinition::UNITS_METRIC === $this->settings[ UpsSettingsDefinition::UNITS ]
			: false;
	}

	/**
	 * Init.
	 */
	public function init() {
		parent::init();

		$box_factory          = new BoxFactory( $this->get_units() );
		$this->fields_factory = new ProMethodFieldsFactory( $this, $box_factory );

		$packer_settings  = new PackerSettings( 'https://www.ups.com/us/en/help-center/packaging-and-supplies/supplies-forms/boxes-and-tubes.page' );
		$packaging_method = $packer_settings->get_packaging_method( $this );

		$packer_factory         = new PackerFactory( $packaging_method );
		$packer                 = $packer_factory->create_packer( $packer_settings->get_shipping_boxes( $this, $box_factory->get_boxes() ) );
		$this->shipping_builder = new WooCommerceShippingBuilder( $packer, $packaging_method, $this->is_unit_metric() );
	}

	/**
	 * Build form fields.
	 */
	public function build_form_fields() {
		$packer_settings   = new PackerSettings( '' );
		$this->instance_form_fields = $packer_settings
			->add_packaging_fields(
				$this->get_settings_definition_from_service( static::$plugin_shipping_decisions ),
				HandlingFeesSettingsDefinitionDecorator::HANDLING_FEES
			)
			->get_form_fields();
		$this->instance_form_fields[ CustomOriginSettingsDefinitionDecorator::OPTION_NAME ]['title'] = ( new InstanceCustomOriginFields( true ) )->get_custom_origin_section_title();
	}

}
