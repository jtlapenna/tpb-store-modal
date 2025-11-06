<?php

/**
 * Request modifier for simple rate.
 *
 * @package WPDesk\UpsProShippingService\SimpleRate
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\SimpleRate;

use UpsProVendor\Psr\Log\LoggerAwareInterface;
use UpsProVendor\Psr\Log\LoggerAwareTrait;
use UpsProVendor\Ups\Entity\SimpleRate;
use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\WPDesk\AbstractShipping\Shipment\Dimensions;
use UpsProVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRateRequestModifier;
/**
 * Can modify request for simple rate.
 */
class SimpleRateRequestModifier implements UpsRateRequestModifier, LoggerAwareInterface
{
    use LoggerAwareTrait;
    const MAX_PACKAGE_WEIGHT = 50;
    const MAX_VOLUME_XS = 100;
    const MAX_VOLUME_S = 250;
    const MAX_VOLUME_M = 650;
    const MAX_VOLUME_L = 1050;
    const MAX_VOLUME_XL = 1728;
    /**
     * @var bool
     */
    private $is_simple_rate_enabled;
    /**
     * @var Shipment
     */
    private $shipment;
    /**
     * @param bool $is_simple_rate_enabled
     * @param Shipment $shipment
     */
    public function __construct(bool $is_simple_rate_enabled, Shipment $shipment)
    {
        $this->is_simple_rate_enabled = $is_simple_rate_enabled;
        $this->shipment = $shipment;
    }
    /**
     * Modify rate request.
     *
     * @param RateRequest $request
     */
    public function modify_rate_request(RateRequest $request)
    {
        try {
            if ($this->should_modify_request($request)) {
                $package = $request->getShipment()->getPackages()[0];
                $simple_rate = new SimpleRate();
                $simple_rate->setCode($this->prepare_simple_rate_code($this->shipment));
                $package->setSimpleRate($simple_rate);
            }
        } catch (UnableToUseSimpleRateException $e) {
            $this->logger->info(sprintf('Unable to use Simple Rate: %1$s', $e->getMessage()));
        }
    }
    /**
     * @param RateRequest $request
     *
     * @return bool
     */
    private function should_modify_request(RateRequest $request)
    {
        if ($this->is_simple_rate_enabled) {
            if (count($request->getShipment()->getPackages()) !== 1) {
                throw new UnableToUseSimpleRateException(__('Shipment has multiple packages!', 'flexible-shipping-ups-pro'));
            }
            if ($this->shipment->ship_from->address->country_code !== 'US' || $this->shipment->ship_to->address->country_code !== 'US') {
                throw new UnableToUseSimpleRateException(__('Sender or receiver address is outside of United States!', 'flexible-shipping-ups-pro'));
            }
            if ($this->shipment->packages[0]->weight->weight > self::MAX_PACKAGE_WEIGHT) {
                throw new UnableToUseSimpleRateException(sprintf(__('Package weight is over %1$d lbs!', 'flexible-shipping-ups-pro'), self::MAX_PACKAGE_WEIGHT));
            }
            return \true;
        }
        return \false;
    }
    /**
     * @param Shipment $shipment
     *
     * @return string
     * @throws UnableToUseSimpleRateException
     */
    private function prepare_simple_rate_code(Shipment $shipment)
    {
        $package = $shipment->packages[0];
        if (!$package->dimensions instanceof Dimensions) {
            throw new UnableToUseSimpleRateException(__('Package has no dimensions!', 'flexible-shipping-ups-pro'));
        }
        if ($package->dimensions->dimensions_unit !== Dimensions::DIMENSION_UNIT_IN) {
            throw new UnableToUseSimpleRateException(sprintf(__('Invalid dimensions units: %1$s!', 'flexible-shipping-ups-pro'), $package->dimensions->dimensions_unit));
        }
        $cubic_inches = $package->dimensions->height * $package->dimensions->length * $package->dimensions->width;
        if ($cubic_inches <= self::MAX_VOLUME_XS) {
            $code = 'XS';
        } elseif ($cubic_inches <= self::MAX_VOLUME_S) {
            $code = 'S';
        } elseif ($cubic_inches <= self::MAX_VOLUME_M) {
            $code = 'M';
        } elseif ($cubic_inches <= self::MAX_VOLUME_L) {
            $code = 'L';
        } elseif ($cubic_inches <= self::MAX_VOLUME_XL) {
            $code = 'XL';
        } else {
            throw new UnableToUseSimpleRateException(sprintf(__('Package is to large: %1$d!', 'flexible-shipping-ups-pro'), $cubic_inches));
        }
        return $code;
    }
}
