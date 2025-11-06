<?php

/**
 * Meta data interpreter.
 *
 * @package WPDesk\WooCommerceShipping\Ups
 */
namespace UpsProVendor\WPDesk\WooCommerceShipping\Ups;

use UpsProVendor\WPDesk\UpsShippingService\UpsShippingService;
use UpsProVendor\WPDesk\View\Renderer\Renderer;
use UpsProVendor\WPDesk\WooCommerceShipping\OrderMetaData\FrontOrderMetaDataDisplay;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\UpsSingleFrontMetaDataInterpreter;
/**
 * Can interpret UPS meta data on order.
 */
class UpsFrontOrderMetaDataDisplay extends FrontOrderMetaDataDisplay
{
    const META_FALLBACK_REASON = 'fallback_reason';
    /**
     * Renderer.
     *
     * @var Renderer
     */
    private $renderer;
    /**
     * UpsOrderMetaDataInterpreter constructor.
     */
    public function __construct(Renderer $renderer)
    {
        parent::__construct(UpsShippingService::UNIQUE_ID);
        $this->renderer = $renderer;
    }
    /**
     * Init interpreters.
     */
    public function init_interpreters()
    {
        $this->add_interpreter(new UpsSingleFrontMetaDataInterpreter(UpsMetaDataBuilder::META_UPS_ACCESS_POINT_ADDRESS, __('UPS Access Point Address', 'flexible-shipping-ups-pro'), 'order-details-after-table-access-point-address', $this->renderer));
    }
}
