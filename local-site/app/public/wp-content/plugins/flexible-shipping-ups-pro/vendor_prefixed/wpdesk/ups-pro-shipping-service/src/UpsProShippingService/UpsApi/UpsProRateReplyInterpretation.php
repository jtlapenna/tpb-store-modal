<?php

/**
 * UPS API: Get response..
 *
 * @package WPDesk\UpsShippingService\UpsApi
 */
namespace UpsProVendor\WPDesk\UpsShippingService\UpsApi;

use UpsProVendor\Ups\Entity\RatedShipment;
use UpsProVendor\Ups\Entity\RateResponse;
use UpsProVendor\Ups\Entity\RateTimeInTransitResponse;
use UpsProVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Get response from API
 */
class UpsProRateReplyInterpretation extends UpsRateReplyInterpretation
{
    /**
     * UpsProRateReplyInterpretation constructor.
     *
     * @param RateResponse $rate_response  Rate response.
     * @param bool         $is_tax_enabled Is tax enabled.
     */
    public function __construct(RateResponse $rate_response, $is_tax_enabled)
    {
        parent::__construct($rate_response, $is_tax_enabled);
    }
    /**
     * Get single rate from rated shipment.
     *
     * @param RatedShipment $rated_shipment .
     *
     * @return SingleRate
     */
    protected function get_single_rate(RatedShipment $rated_shipment)
    {
        $rate = parent::get_single_rate($rated_shipment);
        $this->add_delivery_date_to_rate_if_exists($rate, $rated_shipment);
        return $rate;
    }
    /**
     * Add delivery date ( if exists ) to rate.
     *
     * @param SingleRate $rate .
     * @param RatedShipment $rated_shipment .
     */
    private function add_delivery_date_to_rate_if_exists(SingleRate $rate, RatedShipment $rated_shipment)
    {
        if (isset($rated_shipment->TimeInTransit, $rated_shipment->TimeInTransit->ServiceSummary)) {
            /** @var RateTimeInTransitResponse $time_in_transit */
            $estimated_arrival = $rated_shipment->TimeInTransit->ServiceSummary->getEstimatedArrival();
            $rate->delivery_date = date_create_from_format('YmdHis', $estimated_arrival->getArrival()->getDate() . $estimated_arrival->getArrival()->getTime());
            $rate->business_days_in_transit = intval($estimated_arrival->getBusinessDaysInTransit());
        }
    }
}
