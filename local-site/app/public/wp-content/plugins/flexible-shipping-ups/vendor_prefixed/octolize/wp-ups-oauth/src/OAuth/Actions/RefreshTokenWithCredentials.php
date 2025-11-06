<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions;

use UpsFreeVendor\Octolize\Ups\RestApi\RestApiClient;
use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Exceptions\RefreshTokenException;
use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\TokenOption;
/**
 * Refresh token with credentials.
 *
 * @package Octolize\WooCommerceShipping\Ups\OAuth\Actions
 */
class RefreshTokenWithCredentials implements RefreshToken
{
    private TokenOption $token_option;
    private ?RestApiClient $api_client;
    private string $client_id;
    private string $client_secret;
    public function __construct(TokenOption $token_option, string $client_id, string $client_secret, RestApiClient $api_client = null)
    {
        $this->token_option = $token_option;
        $this->api_client = $api_client;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }
    public function refresh(): void
    {
        try {
            $token = $this->api_client->create_token($this->client_id, $this->client_secret);
            $this->token_option->set($token);
            $this->token_option->update_issued_at_to_current_time_and_set_expires_at();
        } catch (\Exception $e) {
            throw new RefreshTokenException($e->getMessage());
        }
    }
}
