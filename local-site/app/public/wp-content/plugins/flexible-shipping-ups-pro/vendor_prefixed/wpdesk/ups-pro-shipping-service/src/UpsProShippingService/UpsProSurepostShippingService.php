<?php

namespace UpsProVendor\WPDesk\UpsProShippingService;

use UpsProVendor\Ups\Entity\RateResponse;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsProVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use UpsProVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanPack;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\EstimatedDeliverySettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsProRateRequestBuilder;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsProSender;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsProSenderSingleRate;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRestApi\UpsProRestApiSender;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRestApi\UpsProRestApiSenderSingleRate;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsProRateReplyInterpretation;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsRateReplyInterpretation;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsRateRequestBuilder;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsSender;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsSurepostShippingService;
use UpsProVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Shipping service.
 */
class UpsProSurepostShippingService extends UpsSurepostShippingService implements CanPack
{
    /**
     * Get settings
     *
     * @return UpsProSurepostSettingsDefinition
     */
    public function get_settings_definition()
    {
        return new UpsProSurepostSettingsDefinition(parent::get_settings_definition(), parent::get_shop_settings());
    }
    /**
     * Create rate request builder.
     *
     * @param SettingsValues $settings .
     * @param Shipment       $shipment .
     * @param ShopSettings   $shop_settings .
     *
     * @return UpsRateRequestBuilder
     */
    protected function create_rate_request_builder(SettingsValues $settings, Shipment $shipment, ShopSettings $shop_settings)
    {
        return new UpsProRateRequestBuilder($settings, $shipment, $shop_settings, $this->get_logger());
    }
    /**
     * Create sender.
     *
     * @param SettingsValues $settings Settings Values.
     *
     * @return UpsSender
     */
    protected function create_single_rate_sender(SettingsValues $settings, string $surepost_service_code)
    {
        $delivery_dates = $settings->get_value(EstimatedDeliverySettingsDefinitionDecorator::OPTION_DELIVERY_DATES, EstimatedDeliverySettingsDefinitionDecorator::OPTION_NONE);
        $request_time_in_transit = EstimatedDeliverySettingsDefinitionDecorator::OPTION_NONE !== $delivery_dates;
        if ($settings->get_value(UpsSettingsDefinition::API_TYPE, UpsSettingsDefinition::API_TYPE_XML) === UpsSettingsDefinition::API_TYPE_REST) {
            return new UpsProRestApiSenderSingleRate($this->rest_api_client, $surepost_service_code, $this->get_logger(), $this->is_testing($settings), $this->get_shop_settings()->is_tax_enabled(), $request_time_in_transit);
        } else {
            return new UpsProSenderSingleRate($settings->get_value(UpsSettingsDefinition::ACCESS_KEY), $settings->get_value(UpsSettingsDefinition::USER_ID), $settings->get_value(UpsSettingsDefinition::PASSWORD), $surepost_service_code, $this->get_logger(), $this->is_testing($settings), $this->get_shop_settings()->is_tax_enabled(), $request_time_in_transit);
        }
    }
    /**
     * Create reply interpretation.
     *
     * @param RateResponse   $response .
     * @param ShopSettings   $shop_settings .
     * @param SettingsValues $settings .
     *
     * @return UpsRateReplyInterpretation
     */
    protected function create_reply_interpretation(RateResponse $response, $shop_settings, $settings)
    {
        return new UpsProRateReplyInterpretation($response, $shop_settings->is_tax_enabled());
    }
    /**
     * Verify currency.
     *
     * @param string $default_shop_currency Shop currency.
     * @param string $checkout_currency Checkout currency.
     *
     * @return void
     */
    protected function verify_currency($default_shop_currency, $checkout_currency)
    {
        // Do nothing. We currently support multi currency.
    }
}
