<?php

namespace UpsFreeVendor\Octolize\Blocks\PickupPoint\MapPickupPoint;

use UpsFreeVendor\Octolize\Blocks\PickupPoint\CheckoutIntegration;
use UpsFreeVendor\Octolize\Blocks\PickupPoint\Registrator;
use UpsFreeVendor\Octolize\Blocks\PickupPoint\StoreEndpoint;
class MapRegistrator extends Registrator
{
    public function hooks()
    {
        add_action('woocommerce_blocks_checkout_block_registration', function ($integration_registry) {
            $integration_registry->register(new MapCheckoutIntegration($this->get_integration_data(), $this->get_plugin_dir(), $this->get_plugin_file(), \false));
        });
        (new MapStoreEndpoint($this->get_integration_name(), $this->get_meta_data_name()))->hooks();
        (new ShippingRateMetaData())->hooks();
    }
}
