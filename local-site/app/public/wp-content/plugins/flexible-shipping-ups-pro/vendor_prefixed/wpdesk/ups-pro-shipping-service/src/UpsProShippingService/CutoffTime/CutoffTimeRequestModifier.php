<?php

/**
 * Request modifier for cutoff time.
 *
 * @package WPDesk\UpsProShippingService\CutoffTime
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\CutoffTime;

use UpsProVendor\Ups\Entity\Pickup;
use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\WPDesk\AbstractShipping\Settings\BlackoutLeadDays;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRateRequestModifier;
/**
 * Can modify request for cutoff time.
 */
class CutoffTimeRequestModifier implements UpsRateRequestModifier
{
    /**
     * Lead time setting.
     *
     * @var int
     */
    private $lead_time;
    /**
     * Cutoff time setting.
     *
     * @var string
     */
    private $cutoff_time;
    /**
     * @var BlackoutLeadDays
     */
    private $blackout_lead_days;
    /**
     * DestinationAddressTypeRequestModifier constructor.
     *
     * @param int $lead_time .
     * @param string $cutoff_time .
     * @param BlackoutLeadDays $blackout_lead_days .
     */
    public function __construct($lead_time, $cutoff_time, BlackoutLeadDays $blackout_lead_days)
    {
        $this->lead_time = $lead_time;
        $this->cutoff_time = $cutoff_time;
        $this->blackout_lead_days = $blackout_lead_days;
    }
    /**
     * Modify rate request.
     *
     * @param RateRequest $request
     */
    public function modify_rate_request(RateRequest $request)
    {
        if (0 === $this->lead_time) {
            if (!empty($this->cutoff_time)) {
                $cutoff_time = intval($this->cutoff_time);
                $time = current_time('timestamp');
                if (intval(date('H', $time) >= intval($cutoff_time))) {
                    $time = $time + 24 * 60 * 60;
                }
                $pickup = new Pickup();
                $current_date = (new \DateTime())->setTimestamp($time);
                $calculated_date = $this->blackout_lead_days->calculate_date($current_date);
                $pickup->setDate($calculated_date->format('Ymd'));
                $request->getShipment()->getDeliveryTimeInformation()->setPickup($pickup);
            }
        }
    }
}
