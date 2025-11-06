<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth;

use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class ClientCredentialsTokenActions implements Hookable
{
    private string $token_option_name;
    private LoggerInterface $logger;
    public function __construct(string $token_option_name, LoggerInterface $logger)
    {
        $this->token_option_name = $token_option_name;
        $this->logger = $logger;
    }
    public function hooks(): void
    {
        add_action('woocommerce_update_option', [$this, 'delete_client_credentials_token_option']);
    }
    /**
     * @param array $settings
     */
    public function delete_client_credentials_token_option($settings): void
    {
        if (!is_array($settings) || ($settings['id'] ?? '') !== 'woocommerce_flexible_shipping_ups_settings') {
            return;
        }
        try {
            delete_option($this->token_option_name);
        } catch (\Exception $e) {
            $this->logger->error('Error during delete client credentials token option', ['error' => $e->getMessage()]);
        }
    }
}
