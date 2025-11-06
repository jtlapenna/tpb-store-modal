<?php

/**
 * Decorator for collection point flat rate settings.
 *
 * @package WPDesk\UpsProShippingService\CollectionPointFlatRate
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\CollectionPointFlatRate;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
/**
 * Can decorate settings for collection point flat rate.
 */
class CollectionPointFlatRateSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const OPTION_FLAT_RATE_COSTS = 'flat_rate_costs';
    const OPTION_ACCESS_POINT_FLAT_RATE = 'flat_rate';
    public function __construct(SettingsDefinition $ups_settings_definition)
    {
        parent::__construct($ups_settings_definition, UpsSettingsDefinition::ACCESS_POINT, self::OPTION_FLAT_RATE_COSTS, array('title' => __('Flat Rate Cost', 'flexible-shipping-ups-pro'), 'type' => 'price', 'description' => __('Enter the cost for a flat rate. This option allows you to display the Access Points list in checkout without live rates.', 'flexible-shipping-ups-pro'), 'desc_tip' => \true));
    }
    /**
     * Get form fields.
     *
     * @return array
     * @throws \WPDesk\AbstractShipping\Exception\SettingsFieldNotExistsException
     */
    public function get_form_fields()
    {
        $form_fields = $this->add_collection_point_flat_rate_option(parent::get_form_fields());
        return $form_fields;
    }
    /**
     * Add collection point flat rate option.
     *
     * @param array $form_fields
     *
     * @return array
     */
    private function add_collection_point_flat_rate_option(array $form_fields)
    {
        $form_fields[UpsSettingsDefinition::ACCESS_POINT]['options'][self::OPTION_ACCESS_POINT_FLAT_RATE] = __('Access points and flat rate', 'flexible-shipping-ups-pro');
        return $form_fields;
    }
}
