<?php

/**
 * Decorator for estimated delivery settings.
 *
 * @package WPDesk\UpsProShippingService\DestinationAddressType
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\DatesAndTimes\DatesAndTimesSettingsDefinitionDecorator;
/**
 * Can decorate settings for estimated delivery field.
 */
class EstimatedDeliverySettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_DELIVERY_DATES = 'delivery_dates';
    const OPTION_NONE = 'none';
    const OPTION_DELIVERY_DATE = 'delivery_date';
    const OPTION_DAYS_TO_ARRIVAL_DATE = 'days_to_arrival_date';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, DatesAndTimesSettingsDefinitionDecorator::DATES_AND_TIMES_TITLE, self::OPTION_DELIVERY_DATES, array('title' => __('Estimated Delivery', 'flexible-shipping-ups-pro'), 'type' => 'select', 'options' => array(self::OPTION_NONE => __('None', 'flexible-shipping-ups-pro'), self::OPTION_DAYS_TO_ARRIVAL_DATE => __('Show estimated days to delivery date', 'flexible-shipping-ups-pro'), self::OPTION_DELIVERY_DATE => __('Show estimated delivery date', 'flexible-shipping-ups-pro')), 'description' => __('You can show customers an estimated delivery date or time in transit. The information will be added to the service name in the checkout.', 'flexible-shipping-ups-pro'), 'desc_tip' => \true, 'default' => self::OPTION_NONE));
    }
}
