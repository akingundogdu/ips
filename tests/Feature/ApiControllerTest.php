<?php

namespace Tests\Feature;

use App\Http\Helpers\InfusionsoftHelper;
use Tests\TestCase;

/**
 * Class ApiControllerTest
 * @package Tests\Feature
 */
class ApiControllerTest extends TestCase
{

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     * @var string
     */
    private $contact_email = 'email@example.com';

    /**
     * A basic test assign reminder module.
     *
     * @return void
     */
    public function testAssignReminderToModule()
    {
        $this->mockInfusionsoftHelper($this->contact_email, false);

        $response = $this->post(route('api.module_reminder_assigner'), [
            'contact_email' => $this->contact_email
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => ''
        ]);
    }

    /**
     * A basic test assign reminder module with error
     */
    public function testAssignReminderToModuleWithError()
    {
        $this->mockInfusionsoftHelper($this->contact_email);

        $response = $this->post(route('api.module_reminder_assigner'), [
            'contact_email' => $this->contact_email
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Can not found any courses for the specified user.'
        ]);
    }

    /** It's mocking InfusionsoftHelper class.
     * @param string $email
     * @param bool $withError
     */
    private function mockInfusionsoftHelper(string $email, bool $withError = true)
    {
        $this->instance(InfusionsoftHelper::class, \Mockery::mock(InfusionsoftHelper::class, function ($mock) use ($email, $withError) {
            if ($withError) {
                $mock->shouldReceive('getContact')->with($email)->andReturn(false);

                $mock->shouldReceive('addTag')->with(11041, 110)->andReturn(true);
            } else {
                $mock->shouldReceive('getContact')->with($email)->andReturn([
                    "Email" => "5d4419355503f@test.com",
                    "Groups" => "110,154",
                    "_Products" => "ipa,iea",
                    "Id" => 11041
                ]);
                $mock->shouldReceive('addTag')->with(11041, 110)->andReturn(true);
            }
        }));
    }
}
