<?php

/**
 * UPS API: Sender interface.
 *
 * @package WPDesk\UpsShippingService\UpsApi
 */
namespace UpsProVendor\WPDesk\UpsShippingService\UpsApi;

use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\Ups\Entity\RateResponse;
/**
 * Sender class interface.
 */
interface Sender
{
    /**
     * Send request.
     *
     * @param RateRequest $request $request Request.
     *
     * @return RateResponse
     */
    public function send(RateRequest $request);
}
