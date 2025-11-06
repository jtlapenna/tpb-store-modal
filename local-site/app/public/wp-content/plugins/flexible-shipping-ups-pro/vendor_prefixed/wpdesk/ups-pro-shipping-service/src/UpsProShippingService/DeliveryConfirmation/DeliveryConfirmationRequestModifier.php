<?php

/**
 * Request modifier for delivery confirmation.
 *
 * @package WPDesk\UpsProShippingService\DeliveryConfirmation
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\DeliveryConfirmation;

use UpsProVendor\Ups\Entity\DeliveryConfirmation;
use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRateRequestModifier;
/**
 * Can modify request for delivery confirmation.
 */
class DeliveryConfirmationRequestModifier implements UpsRateRequestModifier
{
    /**
     * Delivery confirmation.
     *
     * @var string
     */
    private $delivery_confirmation;
    public function __construct($delivery_confirmation)
    {
        $this->delivery_confirmation = $delivery_confirmation;
    }
    /**
     * Modify rate request.
     *
     * @param RateRequest $request
     */
    public function modify_rate_request(RateRequest $request)
    {
        $delivery_confirmation = new DeliveryConfirmation();
        $delivery_confirmation->setDcisType($this->prepare_delivery_confirmation_value($request, (int) $this->delivery_confirmation));
        if ($this->delivery_confirmation_on_shipment($request)) {
            $request->getShipment()->getShipmentServiceOptions()->setDeliveryConfirmation($delivery_confirmation);
        } else {
            $this->set_delivery_confirmation_on_package($request, $delivery_confirmation);
        }
    }
    /**
     * @param RateRequest $request
     *
     * @return bool
     */
    private function delivery_confirmation_on_shipment(RateRequest $request)
    {
        if ('CA' === $request->getShipment()->getShipFrom()->getAddress()->getCountryCode() && 'CA' === $request->getShipment()->getShipTo()->getAddress()->getCountryCode() || in_array($request->getShipment()->getShipFrom()->getAddress()->getCountryCode(), ['US', 'PR'], \true) && in_array($request->getShipment()->getShipTo()->getAddress()->getCountryCode(), ['US', 'PR'], \true)) {
            return \false;
        }
        return \true;
    }
    /**
     * @param RateRequest $request
     *
     * @return void
     */
    private function set_delivery_confirmation_on_package(RateRequest $request, DeliveryConfirmation $delivery_confirmation)
    {
        foreach ($request->getShipment()->getPackages() as $package) {
            $package->getPackageServiceOptions()->setDeliveryConfirmation($delivery_confirmation);
        }
    }
    /**
     * @param RateRequest $request
     * @param int $delivery_confirmation_value
     *
     * @see https://octolize.youtrack.cloud/agiles/111-60/current?issue=OCT-1409
     *
     * @return int
     */
    private function prepare_delivery_confirmation_value($request, $delivery_confirmation_value)
    {
        if ($this->delivery_confirmation_on_shipment($request)) {
            return $delivery_confirmation_value;
        }
        switch ($delivery_confirmation_value) {
            case (int) DeliveryConfirmationSettingsDefinitionDecorator::DELIVERY_CONFIRMATION_SIGNATURE:
                return 2;
            case (int) DeliveryConfirmationSettingsDefinitionDecorator::DELIVERY_CONFIRMATION_ADULT_SIGNATURE:
                return 3;
            default:
                return $delivery_confirmation_value;
        }
    }
}
