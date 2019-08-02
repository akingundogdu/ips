<?php

namespace Tests\Unit;

use App\Services\ReminderService;
use Tests\TestCase;

class ReminderServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetCourseName()
    {
        $courseKey = 'ipa';
        $reminderService = new ReminderService();

        $result = $reminderService->getCourseName($courseKey);

        $this->assertEquals('IPA', $result);
    }
}
