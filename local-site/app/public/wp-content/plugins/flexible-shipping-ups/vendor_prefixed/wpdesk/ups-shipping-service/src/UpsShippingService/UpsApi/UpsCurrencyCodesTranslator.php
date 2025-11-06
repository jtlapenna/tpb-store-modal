<?php

namespace UpsFreeVendor\WPDesk\UpsShippingService\UpsApi;

class UpsCurrencyCodesTranslator
{
    const CODES = ['RMB' => 'CNY'];
    public function translate_to_woocommerce_currency($code)
    {
        return self::CODES[$code] ?? $code;
    }
}
