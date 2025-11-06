<?php

namespace UpsProVendor\WPDesk\UpsProShippingService\UpsApi\UpsRestApi;

use UpsProVendor\Octolize\Ups\RestApi\Rate;
use UpsProVendor\Octolize\Ups\RestApi\RestApiClient;
use UpsProVendor\Octolize\Ups\RestApi\UpsRestApiSender;
use UpsProVendor\Psr\Log\LoggerInterface;
class UpsProRestApiSender extends UpsRestApiSender
{
    private $rest_api_client;
    /**
     * Is tax enabled.
     *
     * @var bool
     */
    private $is_tax_enabled;
    /**
     * Logger
     *
     * @var LoggerInterface
     */
    private $logger;
    /**
     * Is testing?
     *
     * @var bool
     */
    private $is_testing;
    /**
     * Request time in transit.
     *
     * @var bool
     */
    private $request_time_in_transit;
    public function __construct(RestApiClient $client, LoggerInterface $logger, bool $is_testing = \false, bool $is_tax_enabled = \true, bool $request_time_in_transit = \false)
    {
        parent::__construct($client, $logger, $is_testing, $is_tax_enabled);
        $this->rest_api_client = $client;
        $this->logger = $logger;
        $this->is_testing = $is_testing;
        $this->is_tax_enabled = $is_tax_enabled;
        $this->request_time_in_transit = $request_time_in_transit;
    }
    protected function create_rate()
    {
        return new Rate($this->rest_api_client, $this->logger, $this->is_testing, $this->is_tax_enabled, $this->request_time_in_transit);
    }
}
