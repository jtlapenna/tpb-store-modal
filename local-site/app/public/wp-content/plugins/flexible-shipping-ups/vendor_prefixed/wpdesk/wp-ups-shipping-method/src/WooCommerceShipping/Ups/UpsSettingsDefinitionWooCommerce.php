<?php

/**
 * Settings definitions.
 *
 * @package WPDesk\WooCommerceShipping\Ups
 */
namespace UpsFreeVendor\WPDesk\WooCommerceShipping\Ups;

use UpsFreeVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsFreeVendor\WPDesk\WooCommerceShipping\CustomFields\FieldApiStatusAjax;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
/**
 * Can handle global and instance settings for WooCommerce shipping method.
 */
class UpsSettingsDefinitionWooCommerce extends UpsSettingsDefinition
{
    private $global_method_fields = [UpsSettingsDefinition::API_TYPE, UpsSettingsDefinition::AUTHORIZATION_TYPE, UpsSettingsDefinition::CLIENT_ID, UpsSettingsDefinition::CLIENT_SECRET, UpsSettingsDefinition::OAUTH, UpsSettingsDefinition::SHIPPING_METHOD_TITLE, UpsSettingsDefinition::API_SETTINGS_TITLE, UpsSettingsDefinition::USER_ID, UpsSettingsDefinition::PASSWORD, UpsSettingsDefinition::ACCESS_KEY, UpsSettingsDefinition::ACCOUNT_NUMBER, UpsSettingsDefinition::TESTING, UpsSettingsDefinition::ORIGIN_SETTINGS_TITLE, UpsSettingsDefinition::CUSTOM_ORIGIN, UpsSettingsDefinition::ORIGIN_ADDRESS, UpsSettingsDefinition::ORIGIN_CITY, UpsSettingsDefinition::ORIGIN_POSTCODE, UpsSettingsDefinition::ORIGIN_COUNTRY, UpsSettingsDefinition::ADVANCED_OPTIONS_TITLE, UpsSettingsDefinition::UNITS, UpsSettingsDefinition::DEBUG_MODE, UpsSettingsDefinition::API_STATUS];
    /**
     * Form fields.
     *
     * @var array
     */
    private $form_fields;
    /**
     * UpsSettingsDefinitionWooCommerce constructor.
     *
     * @param array $form_fields Form fields.
     * @param bool $default_api_type_xml Default api type.
     */
    public function __construct(array $form_fields, $default_api_type_xml = \true)
    {
        $this->form_fields = $form_fields;
        if ($default_api_type_xml) {
            $this->form_fields[UpsSettingsDefinition::API_TYPE]['default'] = UpsSettingsDefinition::API_TYPE_XML;
        }
    }
    /**
     * Get form fields.
     *
     * @return array
     */
    public function get_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \false);
    }
    /**
     * Get instance form fields.
     *
     * @return array
     */
    public function get_instance_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \true);
    }
    /**
     * Get global method fields.
     *
     * @return array
     */
    protected function get_global_method_fields()
    {
        return $this->global_method_fields;
    }
    /**
     * Filter instance form fields.
     *
     * @param array $all_fields .
     * @param bool $instance_fields .
     *
     * @return array
     */
    private function filter_instance_fields(array $all_fields, $instance_fields)
    {
        $fields = [];
        foreach ($all_fields as $key => $field) {
            $is_instance_field = !in_array($key, $this->get_global_method_fields(), \true);
            if ($instance_fields && $is_instance_field || !$instance_fields && !$is_instance_field) {
                $fields[$key] = $field;
            }
        }
        return $fields;
    }
}
