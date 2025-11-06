<?php

/**
 * Decorator for rate adjustments title field.
 *
 * @package WPDesk\UpsProShippingService\PickupType
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\RateAdjustmentsTitle;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Can decorate settings by adding pickup type field.
 */
class RateAdjustmentsTitleSettingsDefinitionDecorator extends SettingsDefinitionModifierAfter
{
    const RATE_ADJUSTMENTS_TITLE = 'rate_adjustments_title';
    private ShopSettings $shop_settings;
    /**
     * @param SettingsDefinition $ups_settings_definition
     * @param string $field_id_after
     */
    public function __construct(SettingsDefinition $ups_settings_definition, ShopSettings $shop_settings, $field_id_after = UpsSettingsDefinition::SERVICES)
    {
        $this->shop_settings = $shop_settings;
        parent::__construct($ups_settings_definition, $field_id_after, self::RATE_ADJUSTMENTS_TITLE, $this->get_field_settings());
    }
    /**
     * Get field settings.
     *
     * @return array .
     */
    private function get_field_settings()
    {
        return array('title' => __('Rates Adjustments', 'flexible-shipping-ups-pro'), 'description' => sprintf(__('Adjust these settings to get more accurate rates. Read %swhat affects the UPS rates in UPS WooCommerce plugin →%s', 'flexible-shipping-ups-pro'), sprintf('<a href="%s" target="_blank">', $this->shop_settings->get_locale() === 'pl_PL' ? 'https://octol.io/ups-pro-rates-pl/' : 'https://octol.io/ups-pro-rates-eng/'), '</a>'), 'type' => 'title');
    }
    /**
     * Replaces settings field from free version.
     *
     * @return array .
     * @throws \WPDesk\AbstractShipping\Exception\SettingsFieldNotExistsException
     */
    public function get_form_fields()
    {
        $form_fields = parent::get_form_fields();
        $form_fields[self::RATE_ADJUSTMENTS_TITLE] = $this->get_field_settings();
        return $form_fields;
    }
}
