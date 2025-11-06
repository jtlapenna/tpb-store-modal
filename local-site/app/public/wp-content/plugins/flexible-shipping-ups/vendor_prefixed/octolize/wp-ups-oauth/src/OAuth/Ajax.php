<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth;

use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class Ajax implements Hookable
{
    const AJAX_ACTION_REVOKE = 'ups_revoke';
    /**
     * @var TokenOption
     */
    private $token_option;
    /**
     * @var string
     */
    private $settings_url;
    /**
     * @var string
     */
    private string $app;
    public function __construct(TokenOption $token_option, string $settings_url, string $app = 'live_rates')
    {
        $this->token_option = $token_option;
        $this->settings_url = $settings_url;
        $this->app = $app;
    }
    public static function ajax_action_name(TokenOption $token_option): string
    {
        return 'wpdesk_ajax_' . self::AJAX_ACTION_REVOKE . '_' . $token_option->get_option_name();
    }
    public function hooks()
    {
        add_action('wp_ajax_' . self::ajax_action_name($this->token_option), [$this, 'delete_oauth_data']);
    }
    public function delete_oauth_data()
    {
        if (wp_verify_nonce($_REQUEST['security'], OAuthField::NONCE_ACTION) && current_user_can('manage_woocommerce')) {
            $this->token_option->set([]);
            $status = 'success';
            $message = __('Successfully revoked UPS Authorization.', 'flexible-shipping-ups');
        } else {
            $status = 'error';
            $message = __('Error during revoke authorization.', 'flexible-shipping-ups');
        }
        $security = wp_create_nonce(OAuthField::NONCE_ACTION . $message);
        wp_safe_redirect(admin_url($this->settings_url . '&app=' . $this->app . '&ups-oauth-status=' . $status . '&message=' . urlencode($message) . '&security=' . urlencode($security)));
        if (defined('DOING_AJAX') && \DOING_AJAX) {
            die;
        }
    }
}
