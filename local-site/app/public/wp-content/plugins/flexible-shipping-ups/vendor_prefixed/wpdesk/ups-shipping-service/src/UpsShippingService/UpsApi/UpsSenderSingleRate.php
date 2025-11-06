<?php

/**
 * UPS API: Send request.
 *
 * @package WPDesk\UpsShippingService\UpsApi
 */
namespace UpsFreeVendor\WPDesk\UpsShippingService\UpsApi;

use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\Ups\Entity\Package;
use UpsFreeVendor\Ups\Entity\RateRequest;
use UpsFreeVendor\Ups\Entity\RateResponse;
use UpsFreeVendor\Ups\Entity\UnitOfMeasurement;
use UpsFreeVendor\Ups\Exception\InvalidResponseException;
use UpsFreeVendor\Ups\Rate;
use UpsFreeVendor\WPDesk\AbstractShipping\Exception\RateException;
use UpsFreeVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use UpsFreeVendor\WPDesk\AbstractShipping\Shipment\Weight;
use UpsFreeVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsServices;
/**
 * Send request to UPS API for single rate.
 */
class UpsSenderSingleRate extends UpsSender
{
    const OZS = 'OZS';
    /**
     * @var string
     */
    private $service_code;
    /**
     * UpsSender constructor.
     *
     * @param string          $access_key .
     * @param string          $user_id .
     * @param string          $password
     * @param string          $service_code.
     * @param LoggerInterface $logger Logger.
     * @param bool            $is_testing Is testing?.
     * @param bool            $is_tax_enabled Is tax enabled?.
     */
    public function __construct($access_key, $user_id, $password, $service_code, LoggerInterface $logger, $is_testing = \false, $is_tax_enabled = \true)
    {
        parent::__construct($access_key, $user_id, $password, $logger, $is_testing, $is_tax_enabled);
        $this->service_code = $service_code;
    }
    /**
     * Converts weight to ounces.
     *
     * @param RateRequest $request .
     *
     * @throws UnitConversionException
     */
    public function convert_weight_to_ozs($request)
    {
        foreach ($request->getShipment()->getPackages() as $package) {
            $this->convert_package_weight_to_ozs($package);
        }
        $shipment_total_weight = $request->getShipment()->getShipmentTotalWeight();
        if (null !== $shipment_total_weight) {
            $current_unit = $shipment_total_weight->getUnitOfMeasurement()->getCode() === UnitOfMeasurement::UOM_LBS ? Weight::WEIGHT_UNIT_LBS : Weight::WEIGHT_UNIT_KG;
            if (Weight::WEIGHT_UNIT_LBS === $current_unit) {
                $shipment_weight = (new UniversalWeight((float) $shipment_total_weight->getWeight(), $current_unit))->as_unit_rounded(Weight::WEIGHT_UNIT_OZ);
                $shipment_total_weight->setWeight($shipment_weight);
                $shipment_total_weight->getUnitOfMeasurement()->setCode(self::OZS);
            }
        }
    }
    /**
     * @param Package $package .
     *
     * @throws UnitConversionException
     */
    private function convert_package_weight_to_ozs($package)
    {
        $weight = $package->getPackageWeight();
        if (null !== $weight) {
            $unit = $weight->getUnitOfMeasurement();
            $current_unit = $unit->getCode() === UnitOfMeasurement::UOM_LBS ? Weight::WEIGHT_UNIT_LBS : Weight::WEIGHT_UNIT_KG;
            if (Weight::WEIGHT_UNIT_LBS === $current_unit) {
                $package_weight = (new UniversalWeight((float) $weight->getWeight(), $current_unit))->as_unit_rounded(Weight::WEIGHT_UNIT_OZ);
                $weight->setWeight($package_weight);
                $unit->setCode(self::OZS);
                $weight->setUnitOfMeasurement($unit);
                $package->setPackageWeight($weight);
            }
        }
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
        $rate = $this->create_rate();
        try {
            $request->getShipment()->getService()->setCode($this->service_code);
            if (UpsServices::SUREPOST_LESS_THAN_1_LB === $this->service_code) {
                $this->convert_weight_to_ozs($request);
            }
            $reply = $rate->getRate($request);
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
