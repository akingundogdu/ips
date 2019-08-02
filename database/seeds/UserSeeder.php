<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //I created contact in Infusion system for test.
        User::insert([
            'name' => 'Akin Gundogdu',
            'email' => 'akin-gundogdu@sample-user.com',
            'id' => 11017,
            'password' => '1234'
        ]);
    }
}
