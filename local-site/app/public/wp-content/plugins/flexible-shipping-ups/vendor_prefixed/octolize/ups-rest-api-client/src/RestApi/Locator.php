<?php

namespace UpsFreeVendor\Octolize\Ups\RestApi;

use UpsFreeVendor\Ups\Entity\LocatorRequest;
class Locator extends \UpsFreeVendor\Ups\Locator
{
    use ObjectProperties;
    /**
     * @var false|mixed
     */
    private $is_testing;
    /**
     * @var RestApiClient
     */
    private $client;
    public function __construct($client, $is_testing = \false)
    {
        $this->client = $client;
        $this->is_testing = $is_testing;
    }
    public function getLocations(LocatorRequest $request, $requestOption = self::OPTION_UPS_ACCESS_POINT_LOCATIONS)
    {
        try {
            $response = $this->client->locator($requestOption, $this->prepare_payload($request));
            return $response->LocatorResponse;
        } catch (\Exception $e) {
            $this->getLogger()->error('UPS Locator error: ' . $e->getMessage());
            throw $e;
        }
    }
    private function prepare_payload(LocatorRequest $request): array
    {
        $payload = ['LocatorRequest' => $this->prepare_object_properties($request)];
        if (isset($payload['LocatorRequest']['OriginAddress']['AddressKeyFormat']['AddressLine1'])) {
            $payload['LocatorRequest']['OriginAddress']['AddressKeyFormat']['AddressLine'] = $payload['LocatorRequest']['OriginAddress']['AddressKeyFormat']['AddressLine1'];
            unset($payload['LocatorRequest']['OriginAddress']['AddressKeyFormat']['AddressLine1']);
        }
        if (isset($payload['LocatorRequest']['LocationSearchCriteria']['AccessPointSearch']['PublicAccessPointId'])) {
            $payload['LocatorRequest']['LocationSearchCriteria']['AccessPointSearch']['PublicAccessPointID'] = $payload['LocatorRequest']['LocationSearchCriteria']['AccessPointSearch']['PublicAccessPointId'];
            unset($payload['LocatorRequest']['LocationSearchCriteria']['AccessPointSearch']['PublicAccessPointId']);
        }
        return $payload;
    }
}
