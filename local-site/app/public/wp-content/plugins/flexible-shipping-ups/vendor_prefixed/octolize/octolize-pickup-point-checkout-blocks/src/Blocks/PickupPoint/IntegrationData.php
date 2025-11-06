<?php

namespace UpsFreeVendor\Octolize\Blocks\PickupPoint;

class IntegrationData extends \UpsFreeVendor\Octolize\Blocks\IntegrationData
{
    private const NONCE_NAME = 'nonceName';
    private const NONCE = 'nonce';
    private const AJAX_URL = 'ajaxUrl';
    private const AJAX_ACTION = 'ajaxAction';
    private const LOCAL_INTEGRATION_NAME = 'integrationName';
    private const META_DATA_NAME = 'fieldName';
    private const FLEXIBLE_SHIPPING_INTEGRATION = 'flexibleShippingIntegration';
    public function set_nonce_name(string $nonce_name): IntegrationData
    {
        $this->set_data(self::NONCE_NAME, $nonce_name);
        return $this;
    }
    public function get_nonce_name(): string
    {
        return $this->get_data(self::NONCE_NAME);
    }
    public function set_nonce(string $nonce): IntegrationData
    {
        $this->set_data(self::NONCE, $nonce);
        return $this;
    }
    public function get_nonce(): string
    {
        return $this->get_data(self::NONCE);
    }
    public function set_ajax_url(string $ajax_url): IntegrationData
    {
        $this->set_data(self::AJAX_URL, $ajax_url);
        return $this;
    }
    public function get_ajax_url(): string
    {
        return $this->get_data(self::AJAX_URL);
    }
    public function set_ajax_action(string $ajax_action): IntegrationData
    {
        $this->set_data(self::AJAX_ACTION, $ajax_action);
        return $this;
    }
    public function get_ajax_action(): string
    {
        return $this->get_data(self::AJAX_ACTION);
    }
    public function set_integration_name(string $integration_name): IntegrationData
    {
        $this->set_data(self::LOCAL_INTEGRATION_NAME, $integration_name);
        return $this;
    }
    public function get_integration_name(): string
    {
        return $this->get_data(self::LOCAL_INTEGRATION_NAME);
    }
    public function set_meta_data_name(string $meta_data_name): IntegrationData
    {
        $this->set_data(self::META_DATA_NAME, $meta_data_name);
        return $this;
    }
    public function get_meta_data_name(): string
    {
        return $this->get_data(self::META_DATA_NAME);
    }
    public function set_flexible_shipping_integration(string $flexible_shipping_integration): IntegrationData
    {
        $this->set_data(self::FLEXIBLE_SHIPPING_INTEGRATION, $flexible_shipping_integration);
        return $this;
    }
    public function get_flexible_shipping_integration(): string
    {
        return $this->get_data(self::FLEXIBLE_SHIPPING_INTEGRATION);
    }
}
