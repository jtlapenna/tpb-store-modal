<?php

/**
 * Shipping service.
 *
 * @package WPDesk\UpsProShippingService
 */
namespace UpsProVendor\WPDesk\UpsProShippingService;

use UpsProVendor\Psr\Log\LoggerInterface;
use UpsProVendor\Ups\Entity\RateResponse;
use UpsProVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use UpsProVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsProVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use UpsProVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanPack;
use UpsProVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanReturnDeliveryDate;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\EstimatedDeliverySettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\MaximumTransitTime\MaximumTransitTimeRatesFilter;
use UpsProVendor\WPDesk\UpsProShippingService\MaximumTransitTime\MaximumTransitTimeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsProRateRequestBuilder;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsProSender;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRestApi\UpsProRestApiSender;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsProRateReplyInterpretation;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsRateReplyInterpretation;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsRateRequestBuilder;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsSender;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsShippingService;
use UpsProVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Shipping service.
 */
class UpsProShippingService extends UpsShippingService implements CanReturnDeliveryDate, CanPack
{
    /**
     * Get settings
     *
     * @return UpsProSettingsDefinition
     */
    public function get_settings_definition()
    {
        return new UpsProSettingsDefinition(parent::get_settings_definition(), parent::get_shop_settings());
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
    protected function create_sender(SettingsValues $settings)
    {
        $delivery_dates = $settings->get_value(EstimatedDeliverySettingsDefinitionDecorator::OPTION_DELIVERY_DATES, EstimatedDeliverySettingsDefinitionDecorator::OPTION_NONE);
        $request_time_in_transit = EstimatedDeliverySettingsDefinitionDecorator::OPTION_NONE !== $delivery_dates;
        if ($settings->get_value(UpsSettingsDefinition::API_TYPE, UpsSettingsDefinition::API_TYPE_XML) === UpsSettingsDefinition::API_TYPE_REST) {
            return new UpsProRestApiSender($this->rest_api_client, $this->get_logger(), $this->is_testing($settings), $this->get_shop_settings()->is_tax_enabled(), $request_time_in_transit);
        } else {
            return new UpsProSender($settings->get_value(UpsSettingsDefinition::ACCESS_KEY), $settings->get_value(UpsSettingsDefinition::USER_ID), $settings->get_value(UpsSettingsDefinition::PASSWORD), $this->get_logger(), $this->is_testing($settings), $this->get_shop_settings()->is_tax_enabled(), $request_time_in_transit);
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
     * Decorate rating implementation for maximum transit time if enabled.
     *
     * @param ShipmentRating $shipment_rating .
     * @param SettingsValues $settings .
     *
     * @return ShipmentRating|MaximumTransitTimeRatesFilter
     */
    private function decorate_rating_implementation_for_maximum_transit_time_if_enabled(ShipmentRating $shipment_rating, SettingsValues $settings)
    {
        $delivery_dates = $settings->get_value(EstimatedDeliverySettingsDefinitionDecorator::OPTION_DELIVERY_DATES, EstimatedDeliverySettingsDefinitionDecorator::OPTION_NONE);
        if (EstimatedDeliverySettingsDefinitionDecorator::OPTION_NONE !== $delivery_dates) {
            $maximum_transit_time_setting = $settings->get_value(MaximumTransitTimeSettingsDefinitionDecorator::OPTION_MAXIMUM_TRANSIT_TIME, '');
            if ($maximum_transit_time_setting && is_numeric($maximum_transit_time_setting)) {
                $maximum_transit_time_setting = intval($maximum_transit_time_setting);
                $shipment_rating = new MaximumTransitTimeRatesFilter($shipment_rating, $maximum_transit_time_setting);
            }
        }
        return $shipment_rating;
    }
    /**
     * Create shipment rating implementation.
     *
     * @param SingleRate[]   $rates .
     * @param bool           $is_access_point_rating .
     * @param SettingsValues $settings .
     *
     * @return ShipmentRating
     */
    protected function create_shipment_rating_implementation(array $rates, $is_access_point_rating, $settings)
    {
        $shipment_rating_implementation = parent::create_shipment_rating_implementation($rates, $is_access_point_rating, $settings);
        $shipment_rating_implementation = $this->decorate_rating_implementation_for_maximum_transit_time_if_enabled($shipment_rating_implementation, $settings);
        return $shipment_rating_implementation;
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
