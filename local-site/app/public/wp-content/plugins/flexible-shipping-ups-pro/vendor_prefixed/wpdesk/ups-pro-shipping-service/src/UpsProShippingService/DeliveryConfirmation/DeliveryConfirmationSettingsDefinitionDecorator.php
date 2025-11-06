<?php

/**
 * Decorator for delivery confirmation type settings.
 *
 * @package WPDesk\UpsProShippingService\DeliveryConfirmation
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\DeliveryConfirmation;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\DestinationAddressTypeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
/**
 * Can decorate settings for delivery confirmation.
 */
class DeliveryConfirmationSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_DELIVERY_CONFIRMATION = 'delivery_confirmation';
    const DELIVERY_CONFIRMATION_NONE = 'none';
    const DELIVERY_CONFIRMATION_SIGNATURE = '1';
    const DELIVERY_CONFIRMATION_ADULT_SIGNATURE = '2';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, DestinationAddressTypeSettingsDefinitionDecorator::OPTION_DESTINATION_ADDRESS_TYPE, self::OPTION_DELIVERY_CONFIRMATION, array('title' => __('Delivery Confirmation', 'flexible-shipping-ups-pro'), 'type' => 'select', 'default' => self::DELIVERY_CONFIRMATION_NONE, 'options' => array(self::DELIVERY_CONFIRMATION_NONE => __('None', 'flexible-shipping-ups-pro'), self::DELIVERY_CONFIRMATION_SIGNATURE => __('Signature Required', 'flexible-shipping-ups-pro'), self::DELIVERY_CONFIRMATION_ADULT_SIGNATURE => __('Adult Signature Required', 'flexible-shipping-ups-pro')), 'desc_tip' => __('Select if you want the rates to include the additional UPS Signature Delivery Confirmation service. Choosing the \'Signature Required\' or \'Adult Signature Required\' option here may affect the live rates returned by the UPS API.', 'flexible-shipping-ups-pro')));
    }
}
