<?php

namespace UpsFreeVendor\Octolize\Ups\RestApi;

use UpsFreeVendor\WPDesk\AbstractShipping\Exception\RateException;
class RateExceptionWithExtendedInfo extends RateException
{
    public function __construct($message = '', $context = [], $code = 0, $previous = null)
    {
        parent::__construct($this->extendMessage($message, (int) $code), $context, $code, $previous);
    }
    private function extendMessage(string $message, int $code)
    {
        if (in_array($code, [111035, 111036], \true)) {
            $url = get_user_locale() === 'pl_pl' ? 'https://octol.io/ups-weight-limit-pl' : 'https://octol.io/ups-weight-limit';
            $message .= sprintf(__('%1$sTo calculate rates using multiple boxes, use "Pack items separately" or "Pack into custom boxes". Learn more here: %2$soctolize.com/docs%3$s', 'flexible-shipping-ups'), ' ' . \PHP_EOL, '<a href="' . $url . '" target="_blank">', '</a>');
        }
        return $message;
    }
}
