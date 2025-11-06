<?php

namespace UpsProVendor;

interface WPDesk_Tracker_Sender
{
    /**
     * Sends payload to predefined receiver.
     *
     * @param array $payload Payload to send.
     *
     * @return array If succeeded. Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
     */
    public function send_payload(array $payload);
}
\class_alias('UpsProVendor\WPDesk_Tracker_Sender', 'WPDesk_Tracker_Sender', \false);
