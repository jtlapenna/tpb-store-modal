<?php

/**
 * Decorator for lead time.
 *
 * @package WPDesk\UpsProShippingService\LeadTime
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\LeadTime;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\MaximumTransitTime\MaximumTransitTimeSettingsDefinitionDecorator;
/**
 * Can decorate settings for lead time field.
 */
class LeadTimeSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_LEAD_TIME = 'lead_time';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, MaximumTransitTimeSettingsDefinitionDecorator::OPTION_MAXIMUM_TRANSIT_TIME, self::OPTION_LEAD_TIME, array('title' => __('Lead Time', 'flexible-shipping-ups-pro'), 'type' => 'number', 'description' => __('Lead Time is used to define how many days are required to prepare an order for shipment. The delivery date or time will be updated for the selected number of days.', 'flexible-shipping-ups-pro'), 'desc_tip' => \true, 'default' => '0', 'custom_attributes' => array('min' => 0)));
    }
}
