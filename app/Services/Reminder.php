<?php

namespace App\Services;


/**
 * It manage the decision logic related to Reminder Assigner.
 */
interface Reminder
{
    public function assignModuleReminder(string $customer_email);
}