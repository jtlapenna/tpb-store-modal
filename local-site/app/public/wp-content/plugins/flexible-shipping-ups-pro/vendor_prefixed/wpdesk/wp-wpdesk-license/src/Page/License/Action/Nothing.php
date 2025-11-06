<?php

namespace UpsProVendor\WPDesk\License\Page\License\Action;

use UpsProVendor\WPDesk\License\Page\Action;
/**
 * Do nothing.
 *
 * @package WPDesk\License\Page\License\Action
 */
class Nothing implements Action
{
    public function execute(array $plugin)
    {
        // NOOP
    }
}
