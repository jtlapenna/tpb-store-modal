<?php

namespace UpsFreeVendor\WPDesk\WooCommerceShipping\Ups\Advertisement;

use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsShippingService;
class UpsLabels implements Hookable
{
    public const DISMISSED_OPTION_NAME = 'fs_ups_ups_labels_advert_dismissed';
    public function hooks()
    {
        add_action('woocommerce_after_order_itemmeta', [$this, 'display_ups_label_advertisement'], 10, 2);
    }
    /**
     * Display UPS label advertisement.
     *
     * @param int $item_id
     * @param \WC_Order_Item $item
     */
    public function display_ups_label_advertisement($item_id, $item)
    {
        if (!$item instanceof \WC_Order_Item_Shipping) {
            return;
        }
        if (defined('UpsFreeVendor\WOOCOMMERCE_UPS_LABELS_VERSION')) {
            return;
        }
        if ($item->get_method_id() !== UpsShippingService::UNIQUE_ID) {
            return;
        }
        if (get_option(self::DISMISSED_OPTION_NAME)) {
            return;
        }
        $url = get_locale() === 'pl_PL' ? 'https://octol.io/ups-order-ups-labels-pl' : 'https://octol.io/ups-order-ups-labels';
        include __DIR__ . '/view/html-ups-labels.php';
    }
}
