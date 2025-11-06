<?php

/**
 * UPS API: Get response..
 *
 * @package WPDesk\UpsShippingService\UpsApi
 */
namespace UpsFreeVendor\WPDesk\UpsShippingService\UpsApi;

use UpsFreeVendor\Ups\Entity\RatedShipment;
use UpsFreeVendor\Ups\Entity\RateResponse;
use UpsFreeVendor\WPDesk\AbstractShipping\Rate\Money;
use UpsFreeVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Get response from API
 */
class UpsRateReplyInterpretation
{
    /**
     * Is tax enabled.
     *
     * @var bool
     */
    private $is_tax_enabled;
    /**
     * Reply.
     *
     * @var RateResponse
     */
    private $rate_response;
    /**
     * UpsRateReplyInterpretation constructor.
     *
     * @param RateResponse $rate_response  Rate response.
     * @param bool         $is_tax_enabled Is tax enabled.
     */
    public function __construct(RateResponse $rate_response, $is_tax_enabled)
    {
        $this->rate_response = $rate_response;
        $this->is_tax_enabled = $is_tax_enabled;
    }
    /**
     * Has reply error.
     *
     * @return bool
     */
    public function has_reply_error()
    {
        return \false;
    }
    /**
     * Has reply warning.
     *
     * @return bool
     */
    public function has_reply_warning()
    {
        return \false;
    }
    /**
     * Get reply error message.
     *
     * @return mixed|string
     */
    public function get_reply_message()
    {
        return '';
    }
    /**
     * Maybe get charge from negotiated rates.
     *
     * @param Money         $charges .
     * @param RatedShipment $rated_shipment .
     * @param UpsCurrencyCodesTranslator $currency_codes .
     *
     * @return Money
     */
    private function set_charge_from_negotiated_rates_if_present($charges, $rated_shipment, $currency_codes)
    {
        if (isset($rated_shipment->NegotiatedRates, $rated_shipment->NegotiatedRates->NetSummaryCharges, $rated_shipment->NegotiatedRates->NetSummaryCharges->GrandTotal)) {
            $grand_total = $rated_shipment->NegotiatedRates->NetSummaryCharges->GrandTotal;
            // phpcs:ignore
            $charges->amount = $grand_total->MonetaryValue;
            // phpcs:ignore
            $charges->currency = $currency_codes->translate_to_woocommerce_currency($grand_total->CurrencyCode);
            // phpcs:ignore
        }
        return $charges;
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
        $rate = new SingleRate();
        $charges = new Money();
        $currency_codes = new UpsCurrencyCodesTranslator();
        $charges->currency = $currency_codes->translate_to_woocommerce_currency($rated_shipment->TotalCharges->CurrencyCode);
        // phpcs:ignore
        $charges->amount = $rated_shipment->TotalCharges->MonetaryValue;
        // phpcs:ignore
        $charges = $this->set_charge_from_negotiated_rates_if_present($charges, $rated_shipment, $currency_codes);
        $rate->total_charge = $charges;
        $rate->service_type = $rated_shipment->Service->getCode();
        // phpcs:ignore
        $rate->service_name = $rated_shipment->Service->getCode();
        // phpcs:ignore
        return $rate;
    }
    /**
     * Get response from UPS.
     *
     * @return SingleRate[]
     */
    public function get_rates()
    {
        $rates = [];
        if (!empty($this->rate_response->RatedShipment)) {
            //phpcs:ignore
            foreach ($this->rate_response->RatedShipment as $rated_shipment) {
                $rates[] = $this->get_single_rate($rated_shipment);
            }
        }
        return $rates;
    }
}
