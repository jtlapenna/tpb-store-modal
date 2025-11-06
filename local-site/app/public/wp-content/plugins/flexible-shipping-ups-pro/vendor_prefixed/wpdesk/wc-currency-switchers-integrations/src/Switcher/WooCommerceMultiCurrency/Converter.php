<?php

/**
 * Currency converter.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency
 */
namespace UpsProVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency;

use UpsProVendor\WPDesk\WooCommerce\CurrencySwitchers\AbstractConverter;
/**
 * Can convert currency using WooCommerce MultiCurrency plugin.
 * @see https://woocommerce.com/products/multi-currency/
 */
class Converter extends AbstractConverter
{
    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        try {
            $rate_storage = new \UpsProVendor\WOOMC\Rate\Storage();
            $price_rounder = new \UpsProVendor\WOOMC\Price\Rounder();
            $currency_detector = new \UpsProVendor\WOOMC\Currency\Detector();
            $price_calculator = new \UpsProVendor\WOOMC\Price\Calculator($rate_storage, $price_rounder);
            $price_controller = new \UpsProVendor\WOOMC\Price\Controller($price_calculator, $currency_detector);
            return $price_controller->convert($value);
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->debug($e->getMessage());
            }
        }
        return $value;
    }
}
