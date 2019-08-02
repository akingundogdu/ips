<?php

use App\Module;
use Illuminate\Database\Seeder;

class iPSDevTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 7; $i++) {
            Module::insert([
                [
                    'course_key' => 'ipa',
                    'course_order' => 1,
                    'name' => 'IPA Module ' . $i,
                    'module_order' => $i
                ],

                [
                    'course_key' => 'iea',
                    'course_order' => 2,
                    'name' => 'IEA Module ' . $i,
                    'module_order' => $i
                ],

                [
                    'course_key' => 'iaa',
                    'course_order' => 3,
                    'name' => 'IAA Module ' . $i,
                    'module_order' => $i
                ]
            ]);
        }
    }
}
