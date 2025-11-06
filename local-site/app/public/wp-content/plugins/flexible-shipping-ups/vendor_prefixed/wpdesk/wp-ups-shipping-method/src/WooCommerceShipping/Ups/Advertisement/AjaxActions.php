<?php

namespace UpsFreeVendor\WPDesk\WooCommerceShipping\Ups\Advertisement;

use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class AjaxActions implements Hookable
{
    public const AJAX_ACTION = 'fs_ups_hide_ups_label_adv';
    public function hooks()
    {
        add_action('wp_ajax_' . self::AJAX_ACTION, [$this, 'hide_label_advertisement']);
    }
    public function hide_label_advertisement()
    {
        check_ajax_referer(self::AJAX_ACTION, 'security');
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error();
        }
        update_option(UpsLabels::DISMISSED_OPTION_NAME, \true);
        wp_send_json_success();
    }
}
