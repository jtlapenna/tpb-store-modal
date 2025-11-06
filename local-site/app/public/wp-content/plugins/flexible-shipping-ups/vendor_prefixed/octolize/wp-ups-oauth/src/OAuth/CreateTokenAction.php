<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth;

use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions\CreateToken;
use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Exceptions\CreateTokenException;
use UpsFreeVendor\WPDesk\Notice\Notice;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class CreateTokenAction implements Hookable
{
    use NonceVerifier;
    const BEFORE_DEFAULT_PRIORITY = 9;
    /**
     * @var TokenOption
     */
    private $token_option;
    /**
     * @var string
     */
    private $oauth_url;
    /**
     * @var string
     */
    private $app;
    /**
     * @var string
     */
    private $test_api;
    public function __construct(TokenOption $token_option, string $oauth_url, string $app = 'live_rates', string $test_api = '')
    {
        $this->token_option = $token_option;
        $this->oauth_url = $oauth_url;
        $this->app = $app;
        $this->test_api = $test_api;
    }
    public function hooks()
    {
        add_action('admin_notices', [$this, 'create_token'], self::BEFORE_DEFAULT_PRIORITY);
    }
    public function create_token()
    {
        if (isset($_GET['app']) && $_GET['app'] === $this->app && isset($_GET['ups-oauth-code']) && $this->verify_nonce()) {
            $code = wc_clean($_GET['ups-oauth-code']);
            try {
                $create_token = new CreateToken($code, $this->token_option, $this->oauth_url, $this->app, $this->test_api);
                $create_token->handle();
                new Notice(__('Successfully authorized.', 'flexible-shipping-ups'), Notice::NOTICE_TYPE_SUCCESS);
                do_action('flexible_shipping_ups_token_created');
            } catch (CreateTokenException $e) {
                new Notice($e->getMessage(), Notice::NOTICE_TYPE_ERROR);
            }
        }
    }
}
