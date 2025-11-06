<?php

/**
 * UPS implementation: Validate shipment class.
 *
 * @package WPDesk\UpsShippingService;
 */
namespace UpsFreeVendor\WPDesk\UpsShippingService;

use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\WPDesk\AbstractShipping\Shipment\Shipment;
/**
 * Validate shipment for some cases.
 */
class UpsValidateShipment
{
    /**
     * Shipment.
     *
     * @var Shipment
     */
    private $shipment;
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /**
     * UpsValidateShipment constructor.
     *
     * @param Shipment        $shipment Shipment.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(Shipment $shipment, LoggerInterface $logger)
    {
        $this->shipment = $shipment;
        $this->logger = $logger;
    }
}
