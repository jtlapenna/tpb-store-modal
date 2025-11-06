<?php

namespace UpsProVendor\Octolize\PluginUpdateReminder;

interface Reminder
{
    public function create_reminder(ReminderData $reminder_data): void;
}
