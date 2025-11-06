<?php

namespace UpsFreeVendor\Octolize\Blocks\PickupPoint;

use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class Registrator implements Hookable
{
    private string $integration_name;
    /**
     * @var string
     */
    private string $meta_data_name;
    /**
     * @var string
     */
    private string $plugin_dir;
    /**
     * @var string
     */
    private string $plugin_file;
    private IntegrationData $integration_data;
    public function __construct(IntegrationData $integration_data, string $plugin_dir, string $plugin_file)
    {
        $this->integration_data = $integration_data;
        $this->integration_name = $integration_data->get_integration_name();
        $this->meta_data_name = $integration_data->get_meta_data_name();
        $this->plugin_dir = $plugin_dir;
        $this->plugin_file = $plugin_file;
    }
    public function hooks()
    {
        add_action('woocommerce_blocks_checkout_block_registration', function ($integration_registry) {
            $integration_registry->register(new CheckoutIntegration($this->integration_data, $this->plugin_dir, $this->plugin_file));
        });
        (new StoreEndpoint($this->integration_name, $this->meta_data_name))->hooks();
    }
    protected function get_integration_name(): string
    {
        return $this->integration_name;
    }
    protected function get_meta_data_name(): string
    {
        return $this->meta_data_name;
    }
    protected function get_plugin_dir(): string
    {
        return $this->plugin_dir;
    }
    protected function get_plugin_file(): string
    {
        return $this->plugin_file;
    }
    protected function get_integration_data(): IntegrationData
    {
        return $this->integration_data;
    }
}
