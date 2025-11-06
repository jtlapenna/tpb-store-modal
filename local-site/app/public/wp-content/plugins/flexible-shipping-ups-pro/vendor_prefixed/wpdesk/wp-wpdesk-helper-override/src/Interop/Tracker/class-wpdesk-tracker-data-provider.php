<?php

namespace UpsProVendor;

interface WPDesk_Tracker_Data_Provider
{
    /**
     * Provides data
     *
     * @return array Data provided to tracker.
     */
    public function get_data();
}
\class_alias('UpsProVendor\WPDesk_Tracker_Data_Provider', 'WPDesk_Tracker_Data_Provider', \false);
