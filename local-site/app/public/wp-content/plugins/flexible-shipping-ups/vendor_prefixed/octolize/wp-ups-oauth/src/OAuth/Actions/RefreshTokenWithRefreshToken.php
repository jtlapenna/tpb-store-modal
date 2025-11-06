<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions;

use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Exceptions\RefreshTokenException;
use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\TokenOption;
class RefreshTokenWithRefreshToken implements RefreshToken
{
    /**
     * @var TokenOption
     */
    private TokenOption $token_option;
    /**
     * @var string
     */
    private string $oauth_url;
    /**
     * @var string
     */
    private string $app;
    /**
     * @var bool
     */
    private bool $is_testing;
    public function __construct(TokenOption $token_option, string $oauth_url, string $app = 'live_rates', bool $is_testing = \false)
    {
        $this->token_option = $token_option;
        $this->oauth_url = $oauth_url;
        $this->app = $app;
        $this->is_testing = $is_testing;
    }
    public function refresh(): void
    {
        if (!$this->token_option->get_refresh_token()) {
            throw new RefreshTokenException(__('Refresh token is invalid.', 'flexible-shipping-ups'));
        }
        $response = $this->request_token_refresh();
        if (is_wp_error($response)) {
            throw new RefreshTokenException($response->get_error_message());
        } else {
            $response_body = json_decode(wp_remote_retrieve_body($response), \true);
            if (isset($response_body['status'])) {
                $status = $response_body['status'];
                if ($status === 'error') {
                    throw new RefreshTokenException($response_body['message']);
                } else {
                    $token = $response_body['token'];
                    $this->token_option->set($token);
                    $this->token_option->update_issued_at_to_current_time_and_set_expires_at();
                }
            } else {
                throw new RefreshTokenException(__('Error during refresh.', 'flexible-shipping-ups'));
            }
        }
    }
    /**
     * @return mixed
     */
    private function request_token_refresh()
    {
        return wp_remote_post(sprintf('%s/refresh-token.php?app=%s&test_api=%s', $this->oauth_url, $this->app, $this->is_testing ? '1' : ''), ['body' => ['refresh_token' => $this->token_option->get_refresh_token()]]);
    }
}
