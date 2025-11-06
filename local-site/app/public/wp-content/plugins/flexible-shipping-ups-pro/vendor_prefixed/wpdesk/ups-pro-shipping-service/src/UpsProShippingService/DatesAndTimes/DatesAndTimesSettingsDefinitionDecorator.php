<?php

/**
 * Decorator for dates and times section.
 *
 * @package WPDesk\UpsProShippingService\DatesAndTimes
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\DatesAndTimes;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\PickupType\HandlingFeesSettingsDefinitionDecorator;
/**
 * Can decorate settings by adding handling fees field.
 */
class DatesAndTimesSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const DATES_AND_TIMES_TITLE = 'dates_and_times_title';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, HandlingFeesSettingsDefinitionDecorator::HANDLING_FEES, self::DATES_AND_TIMES_TITLE, array('title' => __('Dates & Time', 'flexible-shipping-ups-pro'), 'description' => __('Manage services\' dates information.', 'flexible-shipping-ups-pro'), 'type' => 'title'));
    }
}
