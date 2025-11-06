<?php

namespace UpsFreeVendor\Octolize\WooCommerceShipping\Ups\OAuth;

trait NonceVerifier
{
    private function verify_nonce($message = ''): bool
    {
        return isset($_GET['security']) && wp_verify_nonce(wc_clean($_GET['security']), OAuthField::NONCE_ACTION . $message);
    }
}
