<?php

/**
 * UPS implementation: UpsShippingService class.
 *
 * @package WPDesk\UpsShippingService
 */
namespace UpsProVendor\WPDesk\UpsShippingService;

use UpsProVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint;
use UpsProVendor\WPDesk\AbstractShipping\Exception\InvalidSettingsException;
use UpsProVendor\WPDesk\AbstractShipping\Exception\RateException;
use UpsProVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use UpsProVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsProVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use UpsProVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRateToCollectionPoint;
use UpsProVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings;
/**
 * Ups main shipping class injected into WooCommerce shipping method.
 */
class UpsShippingService extends AbstractUpsShippingService implements CanRateToCollectionPoint, CanTestSettings
{
    const UNIQUE_ID = 'flexible_shipping_ups';
    /**
     * Is rate to collection point enabled?
     *
     * @param SettingsValues $settings .
     *
     * @return bool
     */
    public function is_rate_to_collection_point_enabled(SettingsValues $settings)
    {
        return UpsSettingsDefinition::DO_NOT_ADD_ACCESS_POINTS_TO_RATES !== $settings->get_value(UpsSettingsDefinition::ACCESS_POINT, UpsSettingsDefinition::DO_NOT_ADD_ACCESS_POINTS_TO_RATES);
    }
    /**
     * Rate shipment.
     *
     * @param SettingsValues $settings Settings.
     * @param Shipment       $shipment Shipment.
     *
     * @return ShipmentRating
     * @throws InvalidSettingsException InvalidSettingsException.
     * @throws RateException RateException.
     * @throws UnitConversionException Weight exception.
     */
    public function rate_shipment(SettingsValues $settings, Shipment $shipment)
    {
        return $this->rate_shipment_for_ups($settings, $shipment, $this->get_services($settings), $this->create_sender($settings));
    }
    /**
     * Rate shipment to collection point.
     *
     * @param SettingsValues  $settings Settings.
     * @param Shipment        $shipment Shipment.
     * @param CollectionPoint $collection_point Collection point.
     *
     * @return ShipmentRating
     * @throws InvalidSettingsException InvalidSettingsException.
     * @throws RateException RateException.
     * @throws UnitConversionException Weight exception.
     */
    public function rate_shipment_to_collection_point(SettingsValues $settings, Shipment $shipment, CollectionPoint $collection_point)
    {
        return $this->rate_shipment_for_ups($settings, $shipment, $this->get_services($settings), $this->create_sender($settings), $collection_point);
    }
    /**
     * Get unique ID.
     *
     * @return string
     */
    public function get_unique_id()
    {
        return self::UNIQUE_ID;
    }
    /**
     * Get name.
     *
     * @return string
     */
    public function get_name()
    {
        return __('UPS Live Rates', 'flexible-shipping-ups-pro');
    }
    /**
     * Get description.
     *
     * @return string
     */
    public function get_description()
    {
        return __('UPS integration', 'flexible-shipping-ups-pro');
    }
}
