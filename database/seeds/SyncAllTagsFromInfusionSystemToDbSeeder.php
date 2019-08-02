<?php

use App\Http\Helpers\InfusionsoftHelper;
use App\Tag;
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
        $tags = (app(InfusionsoftHelper::class))->testInfusionsoftIntegrationGetAllTags();
        Tag::importTags($tags->getData(true));
    }
}
