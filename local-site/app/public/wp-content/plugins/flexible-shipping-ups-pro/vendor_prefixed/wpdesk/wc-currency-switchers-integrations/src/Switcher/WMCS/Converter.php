<?php

/**
 * Currency converter.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers\Switcher\WMCS
 */
namespace UpsProVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WMCS;

use UpsProVendor\WPDesk\WooCommerce\CurrencySwitchers\AbstractConverter;
/**
 * Can convert currency using WMCS plugin.
 * Unknown plugin URL. This is legacy from Flexible Shipping
 */
class Converter extends AbstractConverter
{
    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        try {
            return wmcs_convert_price($value);
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->debug($e->getMessage());
            }
        }
        return $value;
    }
}
