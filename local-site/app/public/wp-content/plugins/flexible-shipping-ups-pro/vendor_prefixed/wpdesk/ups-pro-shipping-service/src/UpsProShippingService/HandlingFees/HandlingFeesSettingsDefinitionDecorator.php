<?php

/**
 * Decorator for handling fees settings field.
 *
 * @package WPDesk\UpsProShippingService\DestinationAddressType
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\PickupType;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\DestinationAddressTypeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees;
/**
 * Can decorate settings by adding handling fees field.
 */
class HandlingFeesSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const HANDLING_FEES = 'handling_fees';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, DestinationAddressTypeSettingsDefinitionDecorator::OPTION_DESTINATION_ADDRESS_TYPE, self::HANDLING_FEES, array('type' => FieldHandlingFees::FIELD_TYPE));
    }
}
