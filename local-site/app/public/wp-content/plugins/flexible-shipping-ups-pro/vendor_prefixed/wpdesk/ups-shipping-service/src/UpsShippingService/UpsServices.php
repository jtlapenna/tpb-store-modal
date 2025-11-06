<?php

/**
 * UPS implementation: UpsServices class.
 *
 * @package WPDesk\UpsShippingService
 */
namespace UpsProVendor\WPDesk\UpsShippingService;

use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsProVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * A class that defines UPS services.
 *
 * @package WPDesk\UpsShippingService
 */
class UpsServices
{
    const SUREPOST_LESS_THAN_1_LB = '92';
    const SUREPOST_1_LB_OR_GREATER = '93';
    /**
     * EU countries.
     *
     * @var array
     */
    private static $eu_countries = [];
    /**
     * Services.
     *
     * @var array
     */
    private static $services = null;
    /**
     * Get services.
     *
     * @return array
     */
    public static function get_services()
    {
        if (empty(self::$services)) {
            self::$services = ['all' => ['96' => __('UPS Worldwide Express Freight', 'flexible-shipping-ups-pro'), '71' => __('UPS Worldwide Express Freight Midday', 'flexible-shipping-ups-pro')], 'other' => ['07' => __('UPS Express', 'flexible-shipping-ups-pro'), '11' => __('UPS Standard', 'flexible-shipping-ups-pro'), '08' => __('UPS Worldwide Expedited', 'flexible-shipping-ups-pro'), '54' => __('UPS Worldwide Express Plus', 'flexible-shipping-ups-pro'), '65' => __('UPS Worldwide Saver', 'flexible-shipping-ups-pro')], 'PR' => [
                // Puerto Rico.
                '02' => __('UPS 2nd Day Air', 'flexible-shipping-ups-pro'),
                '03' => __('UPS Ground', 'flexible-shipping-ups-pro'),
                '01' => __('UPS Next Day Air', 'flexible-shipping-ups-pro'),
                '14' => __('UPS Next Day Air Early', 'flexible-shipping-ups-pro'),
                '08' => __('UPS Worldwide Expedited', 'flexible-shipping-ups-pro'),
                '07' => __('UPS Worldwide Express', 'flexible-shipping-ups-pro'),
                '54' => __('UPS Worldwide Express Plus', 'flexible-shipping-ups-pro'),
                '65' => __('UPS Worldwide Saver', 'flexible-shipping-ups-pro'),
            ], 'PL' => [
                // Poland.
                '70' => __('UPS Access Point Economy', 'flexible-shipping-ups-pro'),
                '83' => __('UPS Today Dedicated Courrier', 'flexible-shipping-ups-pro'),
                '85' => __('UPS Today Express', 'flexible-shipping-ups-pro'),
                '86' => __('UPS Today Express Saver', 'flexible-shipping-ups-pro'),
                '82' => __('UPS Today Standard', 'flexible-shipping-ups-pro'),
                '08' => __('UPS Expedited', 'flexible-shipping-ups-pro'),
                '07' => __('UPS Express', 'flexible-shipping-ups-pro'),
                '54' => __('UPS Express Plus', 'flexible-shipping-ups-pro'),
                '65' => __('UPS Express Saver', 'flexible-shipping-ups-pro'),
                '11' => __('UPS Standard', 'flexible-shipping-ups-pro'),
            ], 'MX' => [
                // Mexico.
                '70' => __('UPS Access Point Economy', 'flexible-shipping-ups-pro'),
                '08' => __('UPS Expedited', 'flexible-shipping-ups-pro'),
                '07' => __('UPS Express', 'flexible-shipping-ups-pro'),
                '11' => __('UPS Standard', 'flexible-shipping-ups-pro'),
                '54' => __('UPS Worldwide Express Plus', 'flexible-shipping-ups-pro'),
                '65' => __('UPS Worldwide Saver', 'flexible-shipping-ups-pro'),
            ], 'EU' => [
                // European Union.
                '70' => __('UPS Access Point Economy', 'flexible-shipping-ups-pro'),
                '08' => __('UPS Expedited', 'flexible-shipping-ups-pro'),
                '07' => __('UPS Express', 'flexible-shipping-ups-pro'),
                '11' => __('UPS Standard', 'flexible-shipping-ups-pro'),
                '54' => __('UPS Worldwide Express Plus', 'flexible-shipping-ups-pro'),
                '65' => __('UPS Worldwide Saver', 'flexible-shipping-ups-pro'),
            ], 'CA' => [
                // Canada.
                '02' => __('UPS Expedited', 'flexible-shipping-ups-pro'),
                '13' => __('UPS Express Saver', 'flexible-shipping-ups-pro'),
                '12' => __('UPS 3 Day Select', 'flexible-shipping-ups-pro'),
                '70' => __('UPS Access Point Economy', 'flexible-shipping-ups-pro'),
                '01' => __('UPS Express', 'flexible-shipping-ups-pro'),
                '14' => __('UPS Express Early', 'flexible-shipping-ups-pro'),
                '65' => __('UPS Express Saver', 'flexible-shipping-ups-pro'),
                '11' => __('UPS Standard', 'flexible-shipping-ups-pro'),
                '08' => __('UPS Worldwide Expedited', 'flexible-shipping-ups-pro'),
                '07' => __('UPS Worldwide Express', 'flexible-shipping-ups-pro'),
                '54' => __('UPS Worldwide Express Plus', 'flexible-shipping-ups-pro'),
            ], 'US' => [
                // USA.
                '11' => __('UPS Standard', 'flexible-shipping-ups-pro'),
                '07' => __('UPS Worldwide Express', 'flexible-shipping-ups-pro'),
                '08' => __('UPS Worldwide Expedited', 'flexible-shipping-ups-pro'),
                '54' => __('UPS Worldwide Express Plus', 'flexible-shipping-ups-pro'),
                '65' => __('UPS Worldwide Saver', 'flexible-shipping-ups-pro'),
                '02' => __('UPS 2nd Day Air', 'flexible-shipping-ups-pro'),
                '59' => __('UPS 2nd Day Air A.M.', 'flexible-shipping-ups-pro'),
                '12' => __('UPS 3 Day Select', 'flexible-shipping-ups-pro'),
                '03' => __('UPS Ground', 'flexible-shipping-ups-pro'),
                '01' => __('UPS Next Day Air', 'flexible-shipping-ups-pro'),
                '14' => __('UPS Next Day Air Early', 'flexible-shipping-ups-pro'),
                '13' => __('UPS Next Day Air Saver', 'flexible-shipping-ups-pro'),
            ]];
        }
        return self::$services;
    }
    /**
     * @return array
     */
    public function get_surepost_services()
    {
        return [self::SUREPOST_LESS_THAN_1_LB => __('Ground Saver Less than 1 lb', 'flexible-shipping-ups-pro'), self::SUREPOST_1_LB_OR_GREATER => __('Ground Saver 1 lb or Greater', 'flexible-shipping-ups-pro'), '94' => __('Ground Saver BPM', 'flexible-shipping-ups-pro'), '95' => __('Ground Saver Media Mail', 'flexible-shipping-ups-pro')];
    }
    /**
     * @return array
     */
    public function get_surepost_same_services()
    {
        return [[self::SUREPOST_LESS_THAN_1_LB, self::SUREPOST_1_LB_OR_GREATER]];
    }
    /**
     * Set EU countries.
     *
     * @param array $eu_countries .
     */
    public static function set_eu_countries(array $eu_countries)
    {
        self::$eu_countries = $eu_countries;
    }
    /**
     * Get services for country.
     *
     * @param string $country_code .
     *
     * @return array
     */
    public static function get_services_for_country($country_code)
    {
        $services = self::get_services();
        $services_for_country = [];
        if (isset($services[$country_code])) {
            $services_for_country = $services[$country_code];
        }
        if ('PL' !== $country_code && in_array($country_code, self::$eu_countries, \true)) {
            $services_for_country = $services['EU'];
        }
        if (0 === count($services_for_country)) {
            $services_for_country = $services['other'];
        }
        foreach ($services['all'] as $key => $value) {
            $services_for_country[$key] = $value;
        }
        return $services_for_country;
    }
}
