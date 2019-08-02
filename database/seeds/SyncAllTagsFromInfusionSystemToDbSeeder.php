<?php

use App\Http\Helpers\HttpClientHelper;
use Illuminate\Database\Seeder;

class SyncAllTagsFromInfusionSystemToDbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $http = new HttpClientHelper();
        $tags = $http->get('api.infusionsoft_test_get_all_tags');
        Tag::importTags($tags);
    }
}
