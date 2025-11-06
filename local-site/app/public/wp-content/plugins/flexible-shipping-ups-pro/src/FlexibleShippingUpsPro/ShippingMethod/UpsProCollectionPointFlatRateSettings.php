<?php
/**
 * Flat rate setting.
 *
 * @package WPDesk\FlexibleShippingUpsPro\ShippingMethod
 */

namespace WPDesk\FlexibleShippingUpsPro\ShippingMethod;

use UpsProVendor\WPDesk\UpsProShippingService\CollectionPointFlatRate\CollectionPointFlatRateSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields;

/**
 * Can modify settings for collection point flat rate.
 *
 * @package WPDesk\FlexibleShippingUpsPro\ShippingMethod
 */
class UpsProCollectionPointFlatRateSettings {

	const CSS_CLASS = 'class';

	const FS_UPS_NO_FLAT_RATE = 'fs-ups-no-flat-rate';
	const FS_UPS_FLAT_RATE    = 'fs-ups-flat-rate';

	const COMMON_FIELDS = [
		UpsSettingsDefinition::METHOD_SETTINGS_TITLE,
		UpsSettingsDefinition::TITLE,
		UpsSettingsDefinition::ACCESS_POINT,
		FreeShippingFields::FIELD_STATUS,
		FreeShippingFields::FIELD_AMOUNT,
	];

	/**
	 * Append CSS class to settings.
	 *
	 * @param array $settings_fields .
	 *
	 * @return array
	 */
	public function append_css_class_to_settings( array $settings_fields ) {
		foreach ( $settings_fields as $field_id => $field ) {
			if ( ! in_array( $field_id, self::COMMON_FIELDS, true ) ) {
				if ( CollectionPointFlatRateSettingsDefinitionDecorator::OPTION_FLAT_RATE_COSTS === $field_id ) {
					$settings_fields[ $field_id ] = $this->append_class_to_field( $field, self::FS_UPS_FLAT_RATE );
				} else {
					$settings_fields[ $field_id ] = $this->append_class_to_field( $field, self::FS_UPS_NO_FLAT_RATE );
				}
			}
		}
		return $settings_fields;
	}

	/**
	 * Append CSS class to settings field.
	 *
	 * @param array  $field .
	 * @param string $class .
	 *
	 * @return array
	 */
	private function append_class_to_field( array $field, $class ) {
		if ( empty( $field[ self::CSS_CLASS ] ) ) {
			$field[ self::CSS_CLASS ] = $class;
		} else {
			$field[ self::CSS_CLASS ] .= ' ' . $class;
		}
		return $field;
	}

}
