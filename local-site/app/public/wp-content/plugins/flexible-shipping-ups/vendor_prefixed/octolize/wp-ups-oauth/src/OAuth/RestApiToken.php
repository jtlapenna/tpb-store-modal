<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth;

use UpsFreeVendor\Octolize\Ups\RestApi\RestApiToken as UpsRestApiToken;
use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions\RefreshToken;
use UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\ActionScheduler\RefreshTokenActionScheduler;
use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\WPDesk\Mutex\WordpressMySQLLockMutex;
class RestApiToken implements UpsRestApiToken
{
    private const LOCK_NAME = 'ups_refresh_token';
    /**
     * @var TokenOption
     */
    private TokenOption $token_option;
    /**
     * @var RefreshToken
     */
    private RefreshToken $refresh_token_action;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var RefreshTokenActionScheduler|null
     */
    private ?RefreshTokenActionScheduler $refresh_token_action_scheduler;
    public function __construct(TokenOption $token_option, RefreshToken $refresh_token_action, LoggerInterface $logger)
    {
        $this->token_option = $token_option;
        $this->refresh_token_action = $refresh_token_action;
        $this->logger = $logger;
    }
    public function set_refresh_token_action_scheduler(RefreshTokenActionScheduler $refresh_token_action_scheduler)
    {
        $this->refresh_token_action_scheduler = $refresh_token_action_scheduler;
    }
    public function get_token(): string
    {
        if ($this->token_option->is_token_expired()) {
            $this->refresh_token();
        }
        return $this->token_option->get_access_token();
    }
    public function refresh_token($force_refresh = \false)
    {
        $mutex = new WordpressMySQLLockMutex(self::LOCK_NAME);
        if ($mutex->acquireLock()) {
            try {
                $this->token_option->clear_wp_cache();
                if ($force_refresh || $this->token_option->is_token_expired()) {
                    $this->refresh_token_action->refresh();
                    $this->logger->debug('Token refreshed', ['token' => $this->token_option->get_access_token()]);
                    do_action('flexible_shipping_ups_token_refreshed');
                } else {
                    $this->logger->debug('Token refreshed in other thread');
                }
            } catch (\Exception $e) {
                $this->token_option->increase_error_count();
                $this->token_option->increase_expiration_time_according_to_error_count();
                $this->logger->error('Refresh token error', ['error' => $e->getMessage(), 'error_count' => $this->token_option->get_error_count(), 'expires_at' => date('Y-m-d H:i:s', $this->token_option->get_expires_at())]);
            } finally {
                $mutex->releaseLock();
            }
        } else {
            $this->logger->debug('Token refresh skipped', ['reason' => 'Lock not acquired']);
        }
    }
}
