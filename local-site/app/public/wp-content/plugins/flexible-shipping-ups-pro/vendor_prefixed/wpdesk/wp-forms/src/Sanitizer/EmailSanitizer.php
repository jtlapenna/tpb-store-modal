<?php

namespace UpsProVendor\WPDesk\Forms\Sanitizer;

use UpsProVendor\WPDesk\Forms\Sanitizer;
class EmailSanitizer implements Sanitizer
{
    public function sanitize($value): string
    {
        return sanitize_email($value);
    }
}
