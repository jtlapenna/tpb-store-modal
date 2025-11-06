<?php

namespace UpsProVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions;

use UpsProVendor\Octolize\Ups\RestApi\RestApiClient;
use UpsProVendor\Octolize\WooCommerceShipping\Ups\OAuth\OAuthUrl;
use UpsProVendor\Octolize\WooCommerceShipping\Ups\OAuth\TokenOption;
use UpsProVendor\Psr\Log\LoggerInterface;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
/**
 * Can create RefreshToken object.
 *
 * @package Octolize\WooCommerceShipping\Ups\OAuth\Actions
 */
class RefreshTokenFactory
{
    public function create(string $authorization_type, string $client_id, string $client_secret, TokenOption $token_option, LoggerInterface $logger): RefreshToken
    {
        if ($authorization_type === UpsSettingsDefinition::CLIENT_CREDENTIALS) {
            $rest_api_client = RestApiClient::create();
            $rest_api_client->setLogger($logger);
            return new RefreshTokenWithCredentials($token_option, $client_id, $client_secret, $rest_api_client);
        }
        return new RefreshTokenWithRefreshToken($token_option, (new OAuthUrl())->get_url());
    }
}
