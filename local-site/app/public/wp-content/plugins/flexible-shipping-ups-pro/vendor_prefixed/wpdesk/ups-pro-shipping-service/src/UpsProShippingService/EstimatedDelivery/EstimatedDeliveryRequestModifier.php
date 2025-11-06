<?php

/**
 * Request modifier for estimated delivery.
 *
 * @package WPDesk\UpsProShippingService\EstimatedDelivery
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\EstimatedDelivery;

use UpsProVendor\Ups\Entity\DeliveryTimeInformation;
use UpsProVendor\Ups\Entity\InvoiceLineTotal;
use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\Ups\Entity\ShipmentTotalWeight;
use UpsProVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRateRequestModifier;
/**
 * Can modify request for destination address type.
 */
class EstimatedDeliveryRequestModifier implements UpsRateRequestModifier
{
    const MIN_INVOICE_TOTAL = 0.01;
    /**
     * Delivery dates.
     *
     * @var string
     */
    private $delivery_dates;
    /**
     * WooCommerce shipment.
     *
     * @var Shipment
     */
    private $shipment;
    /**
     * EstimatedDeliveryRequestModifier constructor.
     *
     * @param string   $delivery_dates .
     * @param Shipment $shipment Shipment.
     */
    public function __construct($delivery_dates, Shipment $shipment)
    {
        $this->delivery_dates = $delivery_dates;
        $this->shipment = $shipment;
    }
    /**
     * Create shipment total weight.
     *
     * @param RateRequest $request .
     *
     * @return ShipmentTotalWeight
     * @throws \Exception
     */
    private function create_shipment_total_weight(RateRequest $request)
    {
        $shipment_total_weight = new ShipmentTotalWeight();
        $total_weight = 0;
        foreach ($request->getShipment()->getPackages() as $package) {
            $package_weight = $package->getPackageWeight();
            $total_weight += floatval($package_weight->getWeight());
            $shipment_total_weight->setUnitOfMeasurement($package_weight->getUnitOfMeasurement());
        }
        $shipment_total_weight->setWeight($total_weight);
        return $shipment_total_weight;
    }
    /**
     * Create invoice line total.
     *
     * @param Shipment $shipment .
     *
     * @return InvoiceLineTotal
     * @throws \Exception
     */
    private function create_invoice_line_total(Shipment $shipment)
    {
        $invoice_line_total = new InvoiceLineTotal();
        $total_value = 0;
        $currency = \false;
        foreach ($shipment->packages as $package) {
            foreach ($package->items as $item) {
                $total_value += $item->declared_value->amount;
                if (!$currency) {
                    $currency = $item->declared_value->currency;
                }
            }
        }
        $invoice_line_total->setCurrencyCode($currency);
        $invoice_line_total->setMonetaryValue(max($total_value, self::MIN_INVOICE_TOTAL));
        return $invoice_line_total;
    }
    /**
     * Modify rate request.
     *
     * @param RateRequest $request
     *
     * @throws \Exception .
     */
    public function modify_rate_request(RateRequest $request)
    {
        $delivery_time_information = new DeliveryTimeInformation();
        $delivery_time_information->setPackageBillType(DeliveryTimeInformation::PBT_NON_DOCUMENT);
        $request->getShipment()->setDeliveryTimeInformation($delivery_time_information);
        $request->getShipment()->setShipmentTotalWeight($this->create_shipment_total_weight($request));
        $request->getShipment()->setInvoiceLineTotal($this->create_invoice_line_total($this->shipment));
    }
}
