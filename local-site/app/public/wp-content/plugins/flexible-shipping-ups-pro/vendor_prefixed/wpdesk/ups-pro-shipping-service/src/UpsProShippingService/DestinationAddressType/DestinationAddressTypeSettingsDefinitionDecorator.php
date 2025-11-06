<?php

/**
 * Decorator for destination address type settings.
 *
 * @package WPDesk\UpsProShippingService\DestinationAddressType
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
/**
 * Can decorate settings for destination address type field.
 */
class DestinationAddressTypeSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_DESTINATION_ADDRESS_TYPE = 'destination_address_type';
    const DESTINATION_ADDRESS_TYPE_COMMERCIAL = 'commercial';
    const DESTINATION_ADDRESS_TYPE_RESIDENTIAL = 'residential';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, UpsSettingsDefinition::PICKUP_TYPE, self::OPTION_DESTINATION_ADDRESS_TYPE, array('title' => __('Destination Address Type', 'flexible-shipping-ups-pro'), 'type' => 'select', 'description' => __('The recipient\'s address is validated by UPS. You can select the type of address for which the rate will be calculated in case of unsuccessful validation.', 'flexible-shipping-ups-pro'), 'default' => self::DESTINATION_ADDRESS_TYPE_COMMERCIAL, 'options' => array(self::DESTINATION_ADDRESS_TYPE_COMMERCIAL => __('Business', 'flexible-shipping-ups-pro'), self::DESTINATION_ADDRESS_TYPE_RESIDENTIAL => __('Residential', 'flexible-shipping-ups-pro')), 'desc_tip' => \true));
    }
}
