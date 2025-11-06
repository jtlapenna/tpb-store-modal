<?php

/**
 * UPS API: Send request.
 *
 * @package WPDesk\UpsProShippingService\UpsApi
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\UpsApi;

use UpsProVendor\Psr\Log\LoggerInterface;
use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\Ups\Entity\RateResponse;
use UpsProVendor\Ups\Exception\InvalidResponseException;
use UpsProVendor\Ups\RateTimeInTransit;
use UpsProVendor\WPDesk\AbstractShipping\Exception\RateException;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsRateReplyInterpretation;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsSender;
/**
 * Send request to UPS API
 */
class UpsProSender extends UpsSender
{
    /**
     * Request time in transit.
     *
     * @var bool
     */
    private $request_time_in_transit;
    /**
     * UpsSender constructor.
     *
     * @param string          $access_key .
     * @param string          $user_id .
     * @param string          $password .
     * @param LoggerInterface $logger Logger.
     * @param bool            $is_testing Is testing?.
     * @param bool            $is_tax_enabled Is tax enabled?.
     * @param bool            $request_time_in_transit Is tax enabled?.
     */
    public function __construct($access_key, $user_id, $password, LoggerInterface $logger, $is_testing = \false, $is_tax_enabled = \true, $request_time_in_transit = \true)
    {
        parent::__construct($access_key, $user_id, $password, $logger, $is_testing, $is_tax_enabled);
        $this->request_time_in_transit = $request_time_in_transit;
    }
    /**
     * Send request.
     *
     * @param RateRequest $request UPS request.
     *
     * @return RateResponse
     *
     * @throws \Exception .
     * @throws RateException .
     */
    public function send(RateRequest $request)
    {
        $rate = new RateTimeInTransit($this->get_access_key(), $this->get_user_id(), $this->get_password(), $this->is_testing(), $this->get_logger());
        try {
            if ($this->request_time_in_transit) {
                $reply = $rate->shopRatesTimeInTransit($request);
            } else {
                $reply = $rate->shopRates($request);
            }
        } catch (InvalidResponseException $e) {
            throw new RateException($e->getMessage(), ['exception' => $e->getCode()]);
            //phpcs:ignore
        }
        $rate_interpretation = new UpsRateReplyInterpretation($reply, $this->is_tax_enabled());
        if ($rate_interpretation->has_reply_error()) {
            throw new RateException($rate_interpretation->get_reply_message(), ['response' => $reply]);
            //phpcs:ignore
        }
        return $reply;
    }
}
