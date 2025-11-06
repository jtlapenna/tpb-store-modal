<?php

namespace UpsFreeVendor\Octolize\Blocks\PickupPoint\MapPickupPoint;

use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class ShippingRateMetaData implements Hookable
{
    public function hooks()
    {
        add_filter('woocommerce_shipping_method_add_rate', [$this, 'add_shipping_rate_meta_data'], 10, 3);
    }
    /**
     * @param \WC_Shipping_Rate $shipping_rate
     * @param array $args
     * @param \WC_Shipping_Method $shipping_method
     *
     * @return \WC_Shipping_Rate
     */
    public function add_shipping_rate_meta_data($shipping_rate, $args, $shipping_method)
    {
        if (method_exists($shipping_method, 'get_option') && method_exists($shipping_rate, 'add_meta_data') && $shipping_method->get_option('map_status') === 'yes') {
            if ($shipping_method->get_option('map_providers')) {
                $shipping_rate->add_meta_data('flexible-shipping-pickup-points-map-providers', $shipping_method->get_option('map_providers'));
            }
            if ($shipping_method->get_option('map_point_types')) {
                $shipping_rate->add_meta_data('flexible-shipping-pickup-points-map-point-types', $shipping_method->get_option('map_point_types'));
            }
        }
        return $shipping_rate;
    }
}
