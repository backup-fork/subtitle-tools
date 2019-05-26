<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SubIdx;
use Faker\Generator as Faker;

$factory->define(SubIdx::class, function (Faker $faker) {
    return [
        'url_key' => generate_url_key(),
        'filename' => $fileName = strtolower(Str::random(12)),
        'store_directory' => 'sub-idx/'.now()->format('Y-z/U').'-'.$fileName.'/',
        'original_name' => $faker->fileName,
        'sub_hash' => sha1(Str::random()),
        'idx_hash' => sha1(Str::random()),
        'sub_file_size' => mt_rand(1000, 1000000),
        'idx_file_size' => mt_rand(1000, 10000),
        'is_readable' => true,
        'last_cache_hit' => null,
        'cache_hits' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
