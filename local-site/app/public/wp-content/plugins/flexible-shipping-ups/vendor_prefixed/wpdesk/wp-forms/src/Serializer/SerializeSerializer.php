<?php

// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions
namespace UpsFreeVendor\WPDesk\Forms\Serializer;

use UpsFreeVendor\WPDesk\Forms\Serializer;
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
