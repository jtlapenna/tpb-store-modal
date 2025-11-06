<?php

namespace UpsProVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions;

interface RefreshToken
{
    public function refresh(): void;
}
