<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use App\Services\ReminderService;

class ApiController extends Controller
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
        try {
            $reminderService->assignModuleReminder($contact_email);
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    private function exampleCustomer()
    {

        $infusionsoft = new InfusionsoftHelper();

        $uniqid = uniqid();

        $infusionsoft->createContact([
            'Email' => $uniqid . '@test.com',
            "_Products" => 'ipa,iea'
        ]);

        $user = User::create([
            'name' => 'Test ' . $uniqid,
            'email' => $uniqid . '@test.com',
            'password' => bcrypt($uniqid)
        ]);

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 5')->first());


        return $user;
    }
}
