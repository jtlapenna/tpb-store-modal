<?php

namespace UpsFreeVendor\Octolize\Blocks\PickupPoint;

class SelectPickupPointException extends \Exception
{
    public function __construct($message = null)
    {
        parent::__construct($message ?? __('Please select a pickup point.', 'flexible-shipping-ups'));
    }
}
