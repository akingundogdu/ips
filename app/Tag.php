<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public static function importTags($tags)
    {
        $tagsArray = array();
        $created_at = Carbon::now();
        foreach ($tags as $tag) {
            $tagsArray[] = ['id' => $tag['id'], 'name' => $tag['name'], 'created_at' => $created_at];
        }
        Tag::insert($tagsArray);
        return $tagsArray;
    }
}
