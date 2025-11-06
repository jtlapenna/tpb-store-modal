<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\ActionScheduler;

use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\RestApiToken;
use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\TokenOption;
use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class RefreshTokenActionScheduler implements Hookable
{
    public const REFRESH_TOKEN_ACTION_NAME = 'flexible_shipping_ups_refresh_token';
    private const TEN_MINUTES = 600;
    private RestApiToken $rest_api_token;
    private LoggerInterface $logger;
    private TokenOption $token_option;
    public function __construct(RestApiToken $rest_api_token, TokenOption $token_option, LoggerInterface $logger)
    {
        $this->rest_api_token = $rest_api_token;
        $this->token_option = $token_option;
        $this->logger = $logger;
        $this->rest_api_token->set_refresh_token_action_scheduler($this);
    }
    public function hooks(): void
    {
        add_action(self::REFRESH_TOKEN_ACTION_NAME, [$this, 'refresh_token']);
        add_action('flexible_shipping_ups_token_created', [$this, 'schedule']);
        add_action('flexible_shipping_ups_token_refreshed', [$this, 'schedule']);
    }
    public function refresh_token(bool $force_refresh = \false): void
    {
        $this->rest_api_token->refresh_token($force_refresh);
    }
    public function enqueue(): void
    {
        $this->logger->debug('Enqueuing refresh token action.');
        if (as_has_scheduled_action(self::REFRESH_TOKEN_ACTION_NAME)) {
            $this->logger->debug('Refresh token action already enqueued.');
            return;
        }
        if (!as_enqueue_async_action(self::REFRESH_TOKEN_ACTION_NAME, [], '', \true)) {
            $this->logger->error('Error during enqueue refresh token action.');
        } else {
            $this->logger->debug('Refresh token action enqueued.');
        }
    }
    public function schedule(): void
    {
        $this->logger->debug('Scheduling refresh token action.');
        as_unschedule_all_actions(self::REFRESH_TOKEN_ACTION_NAME, ['force_refresh' => \true]);
        $this->token_option->clear_wp_cache();
        if (!as_schedule_single_action($this->token_option->get_expires_at() - self::TEN_MINUTES, self::REFRESH_TOKEN_ACTION_NAME, ['force_refresh' => \true])) {
            $this->logger->error('Error during schedule refresh token action.');
        } else {
            $this->logger->debug('Refresh token action scheduled.');
        }
    }
}
