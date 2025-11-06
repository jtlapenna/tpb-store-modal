<?php
/**
 * Shipping method.
 *
 * @package WPDesk\FlexibleShippingUpsPro\ShippingMethod;
 */

namespace WPDesk\FlexibleShippingUpsPro\ShippingMethod;

use Exception;
use UpsProVendor\WPDesk\UpsProShippingService\CollectionPointFlatRate\CollectionPointFlatRateSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\InstanceCustomOrigin\CustomOriginSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\PickupType\HandlingFeesSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsShippingService;
use UpsProVendor\WPDesk\WooCommerceShipping\CustomOrigin\InstanceCustomOriginFields;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasCollectionPointFlatRate;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasCustomOrigin;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasEstimatedDeliveryDates;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasHandlingFees;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasInstanceCustomOrigin;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasRateCache;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits\HandlingFeesTrait;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits\CollectionPointFlatRateTrait;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\UpsShippingMethod;
use UpsProVendor\WPDesk\WooCommerceShippingPro\Packer\PackerFactory;
use UpsProVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings;
use UpsProVendor\WPDesk\WooCommerceShippingPro\ProShippingMethod\ProMethodFieldsFactory;
use UpsProVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder\WooCommerceShippingBuilder;
use WC_Shipping_Method;
use WPDesk\FlexibleShippingUpsPro\PackerBox\BoxFactory;

/**
 * UPS Pro Shipping method.
 */
class UpsProShippingMethod extends UpsShippingMethod implements HasHandlingFees, HasEstimatedDeliveryDates, HasCollectionPointFlatRate, HasRateCache, HasCustomOrigin, HasInstanceCustomOrigin {

	use HandlingFeesTrait;
	use CollectionPointFlatRateTrait;

	/**
	 * Is flat rate enabled.
	 *
	 * @param WC_Shipping_Method $shipping_method .
	 *
	 * @return bool
	 */
	public function is_flat_rate_enabled( $shipping_method ) {
		return $shipping_method->get_option(
			UpsSettingsDefinition::ACCESS_POINT,
			UpsSettingsDefinition::DO_NOT_ADD_ACCESS_POINTS_TO_RATES
		) === CollectionPointFlatRateSettingsDefinitionDecorator::OPTION_ACCESS_POINT_FLAT_RATE;
	}

	/**
	 * Returns flat rate shipping rate suffix.
	 *
	 * @param WC_Shipping_Method $shipping_method .
	 *
	 * @return string
	 */
	public function get_flat_rate_shipping_rate_suffix( $shipping_method ) {
		return 'flat_rate_access_point';
	}

	/**
	 * Generate Settings HTML.
	 *
	 * @param array $form_fields Form fields.
	 * @param bool  $echo        Show or return.
	 *
	 * @return string Generated settings
	 * @throws Exception View doesn't exists.
	 */
	public function generate_settings_html( $form_fields = [], $echo = true ) {
		if ( $this->instance_id ) {
			$form_fields[ CustomOriginSettingsDefinitionDecorator::OPTION_NAME ]['title'] = ( new InstanceCustomOriginFields( true ) )->get_custom_origin_section_title();
			$flat_rate_settings_modifier                                                  = new UpsProCollectionPointFlatRateSettings();
			$form_fields                                                                  = $flat_rate_settings_modifier->append_css_class_to_settings( $form_fields );
		}
		$html = parent::generate_settings_html( $form_fields, $echo );

		if ( $this->instance_id ) {
			add_action( 'admin_footer', [ $this, 'add_footer_scripts' ] );
		}

		if ( $echo ) {
			echo $html; // WPCS: XSS ok.
		} else {
			return $html;
		}

		return $html;
	}

	/**
	 * Add scripts to footer.
	 */
	public function add_footer_scripts() {
		$flat_rate_script = new UpsProCollectionPointFlatRateSettingsScript();
		echo $flat_rate_script->render(); // phpcs:ignore.
	}

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
		$packer_settings   = new PackerSettings( '', '', UpsShippingService::UNIQUE_ID );
		$this->form_fields = $packer_settings
			->add_packaging_fields(
				$this->get_settings_definition_from_service( static::$plugin_shipping_decisions ),
				HandlingFeesSettingsDefinitionDecorator::HANDLING_FEES
			)
			->get_form_fields();
		parent::build_form_fields();
	}

}
