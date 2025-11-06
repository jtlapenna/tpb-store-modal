<?php

namespace UpsProVendor\WPDesk\Forms\Field;

use UpsProVendor\WPDesk\Forms\Sanitizer;
use UpsProVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class InputTextField extends BasicField
{
    public function get_sanitizer(): Sanitizer
    {
        return new TextFieldSanitizer();
    }
    public function get_template_name(): string
    {
        return 'input-text';
    }
}
