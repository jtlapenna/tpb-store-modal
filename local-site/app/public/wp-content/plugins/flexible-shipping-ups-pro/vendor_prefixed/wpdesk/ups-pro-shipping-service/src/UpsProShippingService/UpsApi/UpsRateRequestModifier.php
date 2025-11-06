<?php

/**
 * Rate request modifier.
 *
 * @package WPDesk\UpsProShippingService\UpsApi
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\UpsApi;

use UpsProVendor\Ups\Entity\RateRequest;
/**
 * Interface for UPS rate modifiers.
 */
interface UpsRateRequestModifier
{
    /**
     * Modify rate request.
     *
     * @param RateRequest $request
     */
    public function modify_rate_request(RateRequest $request);
}
