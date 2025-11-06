<?php

namespace UpsFreeVendor\WPDesk\WooCommerceShipping\Ups;

use UpsFreeVendor\WPDesk\UpsShippingService\UpsShippingService;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSurepostSettingsDefinition;
use UpsFreeVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * UPS Shipping Method.
 */
class UpsSurepostShippingMethod extends ShippingMethod implements ShippingMethod\HasFreeShipping, ShippingMethod\HasCustomOrigin
{
    /** @var PluginShippingDecisions */
    protected static $plugin_shipping_decisions;
    /**
     * Supports.
     *
     * @var array
     */
    public $supports = array('shipping-zones', 'instance-settings');
    /**
     * Set shipping service.
     *
     * @param PluginShippingDecisions $plugin_shipping_decisions .
     */
    public static function set_plugin_shipping_decisions(PluginShippingDecisions $plugin_shipping_decisions)
    {
        self::$plugin_shipping_decisions = $plugin_shipping_decisions;
    }
    /**
     * @return PluginShippingDecisions .
     */
    public function get_plugin_shipping_decisions()
    {
        return static::$plugin_shipping_decisions;
    }
    /**
     * Init method.
     */
    public function init()
    {
        parent::init();
        $this->method_description = $this->prepare_description();
    }
    /**
     * Init form fields.
     */
    public function build_form_fields()
    {
        $ups_settings_definition = self::$plugin_shipping_decisions->get_shipping_service()->get_settings_definition();
        $this->instance_form_fields = $ups_settings_definition->get_form_fields();
    }
    /**
     * Create meta data builder.
     *
     * @return UpsMetaDataBuilder
     */
    protected function create_metadata_builder()
    {
        return new UpsMetaDataBuilder($this);
    }
    /**
     * Render shipping method settings.
     */
    public function admin_options()
    {
        parent::admin_options();
        include __DIR__ . '/view/surepost-shipping-method-script.php';
    }
    public function get_option_key()
    {
        return $this->plugin_id . UpsShippingService::UNIQUE_ID . '_settings';
    }
    /**
     * Prepare description.
     * Description depends on current page.
     *
     * @return string
     */
    private function prepare_description()
    {
        if ('pl_PL' === get_locale()) {
            $docs_link = 'https://octol.io/ups-method-docs-pl';
        } else {
            $docs_link = 'https://octol.io/ups-method-docs';
        }
        return sprintf(
            // Translators: docs URL.
            __('Dynamically calculated UPS Ground Saver live rates based on the established UPS API connection. %1$sLearn more →%2$s', 'flexible-shipping-ups'),
            '<a target="_blank" href="' . $docs_link . '">',
            '</a>'
        );
    }
}
