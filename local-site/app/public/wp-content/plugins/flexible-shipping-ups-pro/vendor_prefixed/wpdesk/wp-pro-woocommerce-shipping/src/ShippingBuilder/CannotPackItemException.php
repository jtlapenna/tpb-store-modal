<?php

/**
 * Cannot pack Exception.
 *
 * @package WPDesk\WooCommerceShippingPro\ShippingBuilder.
 */
namespace UpsProVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder;

/**
 * Cannot pack item exception (single item).
 */
class CannotPackItemException extends \RuntimeException
{
    /**
     * CannotPackItemException constructor.
     *
     * @param WC_Product $item .
     * @param string $reason .
     */
    public function __construct($item, $reason)
    {
        // Translators: product names.
        $message = sprintf(__('Cannot pack item: %1$s. %2$s', 'flexible-shipping-ups-pro'), $item->get_name(), $reason);
        parent::__construct($message);
    }
}
