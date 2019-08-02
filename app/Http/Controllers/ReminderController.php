<?php


namespace App\Http\Controllers;

use App\Services\ReminderService;
use Illuminate\Http\Response;

class ReminderController
{
    /**
     * It Calculates & Adds one correct tag to the customer in Infusionsoft.
     *
     * @param ReminderService $reminderService
     * @param string $contact_email
     * @return int
     */
    public function moduleReminderAssigner(ReminderService $reminderService, string $contact_email)
    {
        return 200;
    }
}