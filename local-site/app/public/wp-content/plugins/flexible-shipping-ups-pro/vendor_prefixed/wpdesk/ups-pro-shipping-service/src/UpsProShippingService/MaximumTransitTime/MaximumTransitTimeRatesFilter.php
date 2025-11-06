<?php

/**
 * Rates filter for maximum time in transit.
 *
 * @package WPDesk\UpsProShippingService\MaximumTransitTime
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\MaximumTransitTime;

use UpsProVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use UpsProVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Can filter rates to maximum transit time settings.
 */
class MaximumTransitTimeRatesFilter implements ShipmentRating
{
    /**
     * Rates to filter.
     *
     * @var ShipmentRating
     */
    private $shipment_rating;
    /**
     * Maximum transit time setting.
     *
     * @var int
     */
    private $maximum_transit_time;
    /**
     * MaximumTransitTimeFilteredRates constructor.
     *
     * @param ShipmentRating $shipment_rating Rates .
     * @param int            $maximum_transit_time .
     */
    public function __construct(ShipmentRating $shipment_rating, $maximum_transit_time)
    {
        $this->shipment_rating = $shipment_rating;
        $this->maximum_transit_time = $maximum_transit_time;
    }
    /**
     * Returns filtered rates.
     *
     * @return SingleRate[]
     */
    public function get_ratings()
    {
        $rates = $this->shipment_rating->get_ratings();
        foreach ($rates as $key => $rate) {
            if (isset($rate->business_days_in_transit) && $rate->business_days_in_transit > $this->maximum_transit_time) {
                unset($rates[$key]);
            }
        }
        return $rates;
    }
}
