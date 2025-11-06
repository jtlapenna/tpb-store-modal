<?php

// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions
namespace UpsProVendor\WPDesk\Forms\Serializer;

use UpsProVendor\WPDesk\Forms\Serializer;
class SerializeSerializer implements Serializer
{
    public function serialize($value): string
    {
        return serialize($value);
    }
    public function unserialize(string $value)
    {
        return unserialize($value);
    }
}
