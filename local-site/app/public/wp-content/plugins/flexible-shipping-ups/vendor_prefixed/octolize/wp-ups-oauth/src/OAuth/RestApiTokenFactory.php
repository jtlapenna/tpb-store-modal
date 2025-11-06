<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth;

use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions\RefreshTokenFactory;
use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsShippingService;
class RestApiTokenFactory
{
    use HookableParent;
    public function create(string $authorization_type, string $client_id, string $client_secret, LoggerInterface $logger, string $option_name = 'fs_ups_token', string $settings_url = 'admin.php?page=wc-settings&tab=shipping&section=' . UpsShippingService::UNIQUE_ID, string $app = 'live_rates'): RestApiToken
    {
        $option_name_client_credentials = $option_name . '_client_credentials';
        $token_option = new TokenOption($authorization_type === UpsSettingsDefinition::AUTH_CODE ? $option_name : $option_name_client_credentials);
        $refresh_token_action = (new RefreshTokenFactory())->create($authorization_type, $client_id, $client_secret, $token_option, $logger);
        $rest_api_token = new RestApiToken($token_option, $refresh_token_action, $logger);
        $this->add_hookable(new HookableObjects(new OAuthUrl(), $token_option, $rest_api_token, $logger, $settings_url, $app, '', $option_name_client_credentials));
        $this->hooks_on_hookable_objects();
        return new RestApiToken($token_option, $refresh_token_action, $logger);
    }
}
