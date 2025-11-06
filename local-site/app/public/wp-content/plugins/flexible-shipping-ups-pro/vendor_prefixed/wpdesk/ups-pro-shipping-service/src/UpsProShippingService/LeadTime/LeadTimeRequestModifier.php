<?php

/**
 * Request modifier for lead time.
 *
 * @package WPDesk\UpsProShippingService\LeadTime
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\LeadTime;

use UpsProVendor\Ups\Entity\Pickup;
use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\WPDesk\AbstractShipping\Settings\BlackoutLeadDays;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRateRequestModifier;
/**
 * Can modify request for lead time.
 */
class LeadTimeRequestModifier implements UpsRateRequestModifier
{
    /**
     * @var BlackoutLeadDays
     */
    private $blackout_lead_days;
    /**
     * DestinationAddressTypeRequestModifier constructor.
     *
     * @param BlackoutLeadDays $blackout_lead_days .
     */
    public function __construct(BlackoutLeadDays $blackout_lead_days)
    {
        $this->blackout_lead_days = $blackout_lead_days;
    }
    /**
     * Modify rate request.
     *
     * @param RateRequest $request
     */
    public function modify_rate_request(RateRequest $request)
    {
        $pickup = new Pickup();
        $current_date = (new \DateTime())->setTimestamp(current_time('timestamp'));
        $calculated_date = $this->blackout_lead_days->calculate_date($current_date);
        $pickup->setDate($calculated_date->format('Ymd'));
        $request->getShipment()->getDeliveryTimeInformation()->setPickup($pickup);
    }
}
