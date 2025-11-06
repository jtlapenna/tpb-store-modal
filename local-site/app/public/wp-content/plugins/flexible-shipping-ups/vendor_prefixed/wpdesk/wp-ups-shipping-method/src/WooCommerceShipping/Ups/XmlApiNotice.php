<?php

namespace UpsFreeVendor\WPDesk\WooCommerceShipping\Ups;

use UpsFreeVendor\WPDesk\Notice\Notice;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
class XmlApiNotice implements Hookable
{
    private array $ups_settings;
    public function __construct(array $ups_settings)
    {
        $this->ups_settings = $ups_settings;
    }
    public function hooks()
    {
        add_action('admin_notices', array($this, 'ups_xml_api_notice'));
    }
    public function ups_xml_api_notice()
    {
        if (UpsSettingsDefinition::API_TYPE_XML === ($this->ups_settings[UpsSettingsDefinition::API_TYPE] ?? UpsSettingsDefinition::API_TYPE_XML)) {
            $settings_url = admin_url('admin.php?page=wc-settings&tab=shipping&section=flexible_shipping_ups');
            ob_start();
            include __DIR__ . '/view/xml-api-notice.php';
            $content = ob_get_contents();
            ob_end_clean();
            new Notice($content, 'warning');
        }
    }
}
