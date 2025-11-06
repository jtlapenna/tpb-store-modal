<?php

namespace UpsFreeVendor\WPDesk\WooCommerceShipping\Ups;

use UpsFreeVendor\WPDesk\Notice\Notice;
use UpsFreeVendor\WPDesk\Notice\PermanentDismissibleNotice;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
class AuthCodeNotice implements Hookable
{
    private array $ups_settings;
    public function __construct(array $ups_settings)
    {
        $this->ups_settings = $ups_settings;
    }
    public function hooks()
    {
        add_action('admin_notices', array($this, 'auth_code_notice'));
    }
    public function auth_code_notice()
    {
        if (UpsSettingsDefinition::AUTH_CODE === ($this->ups_settings[UpsSettingsDefinition::AUTHORIZATION_TYPE] ?? UpsSettingsDefinition::AUTH_CODE)) {
            new PermanentDismissibleNotice(sprintf(__('We have detected that you are using Auth Code as the authorization method in UPS. This method should only be used for testing purposes. Please switch to Client Credentials. %1$sGo to your UPS settings &rarr;%2$s', 'flexible-shipping-ups'), sprintf('<a href="%1$s">', admin_url('admin.php?page=wc-settings&tab=shipping&section=flexible_shipping_ups')), '</a>'), 'ups_auth_code_notice_' . date('YW'), 'error');
        }
    }
}
