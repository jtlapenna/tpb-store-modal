<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth\Actions;

interface RefreshToken
{
    public function refresh(): void;
}
