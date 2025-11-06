<?php

/**
 * Decorator for instance custom origin settings.
 *
 * @package WPDesk\UpsProShippingService\DestinationAddressType
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\InstanceCustomOrigin;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\EstimatedDeliverySettingsDefinitionDecorator;
/**
 * Can decorate settings for estimated delivery field.
 */
class CustomOriginSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_NAME = 'custom_origin_title';
    /**
     * @param SettingsDefinition $ups_settings_definition
     * @param string $field_id_after
     */
    public function __construct(SettingsDefinition $ups_settings_definition, $field_id_after)
    {
        parent::__construct($ups_settings_definition, $field_id_after, self::OPTION_NAME, array('title' => __('Origin Settings', 'flexible-shipping-ups-pro'), 'type' => 'title'));
    }
}
