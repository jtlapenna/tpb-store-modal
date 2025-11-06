<?php

/**
 * Request modifier for address type.
 *
 * @package WPDesk\UpsProShippingService\DestinationAddressType
 */
namespace UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType;

use UpsProVendor\Ups\Entity\RateRequest;
use UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRateRequestModifier;
/**
 * Can modify request for destination address type.
 */
class DestinationAddressTypeRequestModifier implements UpsRateRequestModifier
{
    /**
     * Pickup type code.
     *
     * @var string
     */
    private $destination_address_type;
    /**
     * DestinationAddressTypeRequestModifier constructor.
     *
     * @param string $destination_address_type .
     */
    public function __construct($destination_address_type)
    {
        $this->destination_address_type = $destination_address_type;
    }
    /**
     * Modify rate request.
     *
     * @param RateRequest $request
     */
    public function modify_rate_request(RateRequest $request)
    {
        if ($this->destination_address_type === DestinationAddressTypeSettingsDefinitionDecorator::DESTINATION_ADDRESS_TYPE_RESIDENTIAL) {
            $request->getShipment()->getShipTo()->getAddress()->setResidentialAddressIndicator('T');
        }
    }
}
