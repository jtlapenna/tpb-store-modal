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
class InstanceCustomOriginSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_NAME = 'instance_custom_origin';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, CustomOriginSettingsDefinitionDecorator::OPTION_NAME, self::OPTION_NAME, array('type' => 'instance_custom_origin'));
    }
}
