<?php

/**
 * Decorator for maximum transit time.
 *
 * @package WPDesk\UpsProShippingService\MaximumTransitTime
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\MaximumTransitTime;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\EstimatedDeliverySettingsDefinitionDecorator;
/**
 * Can decorate settings for maximum transit time field.
 */
class MaximumTransitTimeSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_MAXIMUM_TRANSIT_TIME = 'maximum_transit_time';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, EstimatedDeliverySettingsDefinitionDecorator::OPTION_DELIVERY_DATES, self::OPTION_MAXIMUM_TRANSIT_TIME, array('title' => __('Maximum Time in Transit', 'flexible-shipping-ups-pro'), 'type' => 'number', 'description' => __('Maximum Time in Transit is used to define the number of maximum days goods can be in transit. Only days in transit are counted. This is often used for perishable goods.', 'flexible-shipping-ups-pro'), 'desc_tip' => \true, 'custom_attributes' => array('min' => '0')));
    }
}
