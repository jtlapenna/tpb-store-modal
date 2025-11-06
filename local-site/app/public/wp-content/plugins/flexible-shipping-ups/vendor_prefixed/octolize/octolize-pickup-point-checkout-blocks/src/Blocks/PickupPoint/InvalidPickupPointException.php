<?php

namespace UpsFreeVendor\Octolize\Blocks\PickupPoint;

class InvalidPickupPointException extends \Exception
{
    public function __construct($message = null)
    {
        parent::__construct($message ?? __('Invalid pickup point selected.', 'flexible-shipping-ups'));
    }
}
