<?php

namespace UpsFreeVendor\WPDesk\RepositoryRating\DisplayStrategy;

/**
 * DisplayDecision - always display.
 */
class AlwaysDisplayDisplayDecision implements DisplayDecision
{
    /**
     * Should display?
     *
     * @return bool
     */
    public function should_display(): bool
    {
        return \true;
    }
}
