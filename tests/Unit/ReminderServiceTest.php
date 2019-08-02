<?php

namespace Tests\Unit;

use App\Http\Helpers\InfusionsoftHelper;
use App\Services\ReminderService;
use Tests\TestCase;

class ReminderServiceTest extends TestCase
{

    public function testGetCourseName()
    {
        $courseKey = 'ipa';
        $reminderService = $this->getReminderServiceInstance();
        $result = $reminderService->getCourseName($courseKey);
        $this->assertEquals('IPA', $result);
    }


    public function testGetCustomersCourses()
    {
        $customer_email = 'email@example.com';
        $this->instance(InfusionsoftHelper::class, \Mockery::mock(InfusionsoftHelper::class, function ($mock) use ($customer_email) {

            $mock->shouldReceive('getContact')->with($customer_email)->andReturn([
                "Email" => $customer_email,
                "Groups" => "110,154",
                "_Products" => "ipa,iea",
                "Id" => 11041
            ]);
        }));

        $reminderService = $this->getReminderServiceInstance();
        $result = $reminderService->getCustomersCourses($customer_email);
        $this->assertEquals([0 => 'ipa', 1 => 'iea'], $result);
    }


    public function testGetCustomersCoursesWithEmailError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Customer email should not be null');

        $customer_email = '';
        $reminderService = $this->getReminderServiceInstance();
        $reminderService->getCustomersCourses($customer_email);
    }


    public function testGetCustomersCoursesWithoutCourse()
    {
        $customer_email = 'email@example.com';
        $this->instance(InfusionsoftHelper::class, \Mockery::mock(InfusionsoftHelper::class, function ($mock) use ($customer_email) {
            $mock->shouldReceive('getContact')->with($customer_email)->andReturn([
                "_Products" => ""
            ]);
        }));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can not found any courses for the specified user.');

        $reminderService = $this->getReminderServiceInstance();
        $reminderService->getCustomersCourses($customer_email);
    }

    private function getReminderServiceInstance()
    {
        return new ReminderService();
    }
}
