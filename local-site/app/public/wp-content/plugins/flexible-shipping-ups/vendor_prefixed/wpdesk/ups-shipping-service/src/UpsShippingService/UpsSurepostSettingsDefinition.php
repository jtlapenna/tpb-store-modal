<?php

namespace UpsFreeVendor\WPDesk\UpsShippingService;

use UpsFreeVendor\Ups\Entity\PickupType;
use UpsFreeVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsFreeVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsFreeVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * A class that defines the basic settings for the shipping method.
 *
 * @package WPDesk\UpsShippingService
 */
class UpsSurepostSettingsDefinition extends SettingsDefinition
{
    const METHOD_SETTINGS_TITLE = 'method_settings_title';
    const TITLE = 'title';
    const FALLBACK = 'fallback';
    const SUREPOST_SERVICES = 'surepost_services';
    const RATE_ADJUSTMENTS_TITLE = 'rate_adjustments_title';
    const NEGOTIATED_RATES = 'negotiated_rates';
    const INSURANCE = 'insurance';
    const PICKUP_TYPE = 'pickup_type';
    const FREE_SHIPPING = 'free_shipping';
    const NOT_SET = 'not_set';
    const DEFAULT_PICKUP_TYPE = PickupType::PKT_CUSTOMERCOUNTER;
    private ShopSettings $shop_settings;
    public function __construct(ShopSettings $shop_settings)
    {
        $this->shop_settings = $shop_settings;
    }
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings.
     *
     * @return bool
     */
    public function validate_settings(SettingsValues $settings)
    {
        return \true;
    }
    /**
     * Initialise Settings Form Fields.
     */
    public function get_form_fields()
    {
        $ups_services = new UpsServices();
        $instance_fields = array(self::METHOD_SETTINGS_TITLE => array('title' => __('Method Settings', 'flexible-shipping-ups'), 'description' => __('Set how UPS services are displayed.', 'flexible-shipping-ups'), 'type' => 'title'), self::TITLE => array('title' => __('Method Title', 'flexible-shipping-ups'), 'type' => 'text', 'description' => __('This controls the title which the user sees during checkout when fallback is used.', 'flexible-shipping-ups'), 'default' => __('UPS Ground Saver Live Rates', 'flexible-shipping-ups'), 'desc_tip' => \true), self::FALLBACK => array('type' => FallbackRateMethod::FIELD_TYPE_FALLBACK, 'default' => ''), self::FREE_SHIPPING => array('title' => __('Free Shipping', 'flexible-shipping-ups'), 'type' => FreeShippingFields::FIELD_TYPE_FREE_SHIPPING, 'default' => ''), self::SUREPOST_SERVICES => array('title' => __('Services', 'flexible-shipping-ups'), 'type' => 'services', 'options' => $ups_services->get_surepost_services(), 'default' => ''), self::RATE_ADJUSTMENTS_TITLE => array('title' => __('Rates Adjustments', 'flexible-shipping-ups'), 'description' => sprintf(__('Adjust these settings to get more accurate rates. Read %swhat affects the UPS rates in UPS WooCommerce plugin →%s', 'flexible-shipping-ups'), sprintf('<a href="%s" target="_blank">', 'pl_PL' === $this->shop_settings->get_locale() ? 'https://octol.io/ups-free-rates-pl/' : 'https://octol.io/ups-free-rates-eng/'), '</a>'), 'type' => 'title'), self::NEGOTIATED_RATES => array('title' => __('Negotiated Rates', 'flexible-shipping-ups'), 'label' => __('Enable negotiated rates', 'flexible-shipping-ups'), 'type' => 'checkbox', 'description' => __('Enable this option only if your shipping account has negotiated rates available.', 'flexible-shipping-ups'), 'desc_tip' => \true, 'default' => 'no'), self::PICKUP_TYPE => array('title' => __('Pickup Type', 'flexible-shipping-ups'), 'type' => 'select', 'description' => __('\'Pickup Type\' may affect the live rates. In most cases selecting the \'Customer Counter\' or \'One Time Pickup\' grants the most accurate rates. If the \'Not set\' option has been chosen, the \'Pickup Type\' value will not be sent in the UPS API request.', 'flexible-shipping-ups'), 'desc_tip' => \true, 'default' => self::DEFAULT_PICKUP_TYPE, 'options' => array(self::NOT_SET => __('Not set', 'flexible-shipping-ups'), PickupType::PKT_DAILY => __('Daily Pickup', 'flexible-shipping-ups'))));
        return $instance_fields;
    }
}
