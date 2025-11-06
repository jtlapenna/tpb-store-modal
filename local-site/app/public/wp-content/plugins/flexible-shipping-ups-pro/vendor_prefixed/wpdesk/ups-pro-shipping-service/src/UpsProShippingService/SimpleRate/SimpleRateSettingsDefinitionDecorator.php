<?php

/**
 * Decorator for simple rate settings.
 *
 * @package WPDesk\UpsProShippingService\SimpleRate
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\SimpleRate;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierBefore;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\DatesAndTimes\DatesAndTimesSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsShippingService;
/**
 * Can decorate settings for simple rate.
 */
class SimpleRateSettingsDefinitionDecorator extends SettingsDefinitionModifierBefore
{
    const OPTION_SIMPLE_RATE = 'simple_rate';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, UpsSettingsDefinition::INSURANCE, self::OPTION_SIMPLE_RATE, ['title' => __('Simple Rate', 'flexible-shipping-ups-pro'), 'label' => __('Enable Simple Rate', 'flexible-shipping-ups-pro'), 'type' => 'checkbox', 'desc_tip' => sprintf(__('Tick this checkbox to use Simple Rate instead of standard or account-specific rates.%1$sOption only available for U.S. domestic shipments based on sender’s and recipient’s address.', 'flexible-shipping-ups-pro'), '<br/>'), 'description' => sprintf(__('Services valid for Simple Rate: UPS Ground, UPS 3 Day Select, UPS Second Day Air, UPS Next Day Air Saver.%1$sThe parcel includes one package according to the criteria specified by UPS. Each parcel must have defined dimensions, choose %2$sPack into custom boxes%3$s in %2$sParcel Packaging Method%3$s section.%1$s%4$sLearn more about Simple Rate here ->%5$s', 'flexible-shipping-ups-pro'), '<br/>', '<i>', '</i>', '<a href="https://octol.io/ups-simple-rate" target="_blank">', '</a>')]);
    }
}
