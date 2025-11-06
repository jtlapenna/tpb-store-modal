<?php

/**
 * Abstract converter.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers
 */
namespace UpsProVendor\WPDesk\WooCommerce\CurrencySwitchers;

use UpsProVendor\Psr\Log\LoggerAwareInterface;
use UpsProVendor\Psr\Log\LoggerAwareTrait;
/**
 * Abstract class for converters.
 */
abstract class AbstractConverter implements SwitcherConverter, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @inheritDoc
     */
    abstract function convert($value);
    /**
     * @inheritDoc
     */
    public function convert_array($values)
    {
        foreach ($values as $key => $value) {
            if ($value) {
                $values[$key] = $this->convert($value);
            }
        }
        return $values;
    }
}
