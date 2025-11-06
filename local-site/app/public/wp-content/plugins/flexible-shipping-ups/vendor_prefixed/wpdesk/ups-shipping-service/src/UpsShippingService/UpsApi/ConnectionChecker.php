<?php

/**
 * Connection checker.
 *
 * @package WPDesk\UpsShippingService\UpsApi
 */
namespace UpsFreeVendor\WPDesk\UpsShippingService\UpsApi;

use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\Ups\Entity\Address;
use UpsFreeVendor\Ups\Entity\Package;
use UpsFreeVendor\Ups\Entity\PackageWeight;
use UpsFreeVendor\Ups\Entity\RateRequest;
use UpsFreeVendor\Ups\Entity\UnitOfMeasurement;
use UpsFreeVendor\Ups\Rate;
use UpsFreeVendor\Ups\SimpleAddressValidation;
use UpsFreeVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsFreeVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsShippingService;
/**
 * Can check connection.
 */
class ConnectionChecker
{
    /**
     * Shipping service.
     *
     * @var UpsShippingService
     */
    private $shipping_service;
    /**
     * Settings.
     *
     * @var SettingsValuesAsArray
     */
    private $settings;
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Sender
     */
    private $sender;
    /**
     * ConnectionChecker constructor.
     *
     * @param UpsShippingService $shipping_service .
     * @param SettingsValues     $settings .
     * @param LoggerInterface    $logger .
     */
    public function __construct(UpsShippingService $shipping_service, Sender $sender, SettingsValues $settings, $logger)
    {
        $this->shipping_service = $shipping_service;
        $this->settings = $settings;
        $this->logger = $logger;
        $this->sender = $sender;
    }
    /**
     * Pings API.
     *
     * @throws \Exception .
     */
    public function check_connection()
    {
        $address = new Address();
        $address->setStateProvinceCode('NY');
        $address->setCity('New York');
        $address->setCountryCode('US');
        $address->setPostalCode('10000');
        $request = new RateRequest();
        $package = new Package();
        $weight = new PackageWeight();
        $weight->setWeight(1);
        $weight->getUnitOfMeasurement()->setCode(UnitOfMeasurement::UOM_LBS);
        $package->setPackageWeight($weight);
        $request->getShipment()->addPackage($package);
        $request->getShipment()->getShipper()->setAddress($address);
        $request->getShipment()->getShipTo()->setAddress($address);
        $this->sender->send($request);
    }
}
