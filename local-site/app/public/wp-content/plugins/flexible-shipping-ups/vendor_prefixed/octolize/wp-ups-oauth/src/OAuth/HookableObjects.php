<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth;

use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\ActionScheduler\RefreshTokenActionScheduler;
use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
class HookableObjects implements HookableCollection
{
    use HookableParent;
    private OAuthUrl $oauth_url;
    private TokenOption $token_option;
    private string $settings_url;
    private string $app;
    private string $test_api;
    private RestApiToken $rest_api_token;
    private LoggerInterface $logger;
    private string $client_credentials_option_name;
    public function __construct(OAuthUrl $oauth_url, TokenOption $token_option, RestApiToken $rest_api_token, LoggerInterface $logger, string $settings_url, string $app = 'live_rates', string $test_api = '', $client_credentials_option_name = '')
    {
        $this->oauth_url = $oauth_url;
        $this->token_option = $token_option;
        $this->rest_api_token = $rest_api_token;
        $this->logger = $logger;
        $this->settings_url = $settings_url;
        $this->app = $app;
        $this->test_api = $test_api;
        $this->client_credentials_option_name = $client_credentials_option_name;
    }
    public function hooks()
    {
        $this->add_hookable(new Notices($this->token_option, $this->oauth_url->get_url(), $this->app));
        $this->add_hookable(new CreateTokenAction($this->token_option, $this->oauth_url->get_url(), $this->app, $this->test_api));
        $this->add_hookable(new Ajax($this->token_option, $this->settings_url, $this->app));
        $this->add_hookable(new RefreshTokenActionScheduler($this->rest_api_token, $this->token_option, $this->logger));
        if (!empty($this->client_credentials_option_name)) {
            $this->add_hookable(new ClientCredentialsTokenActions($this->client_credentials_option_name, $this->logger));
        }
        $this->hooks_on_hookable_objects();
    }
}
