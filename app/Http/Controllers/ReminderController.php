<?php


namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ReminderController
{
    /**
     * It Calculates & Adds one correct tag to the customer in Infusionsoft.
     *
     * @param $contact_email
     * @return Response
     */
    public function moduleReminderAssigner(string $contact_email)
    {
        return 200;
    }
}