<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SubIdxStats;
use Faker\Generator as Faker;

$factory->define(SubIdxStats::class, function (Faker $faker) {
    return [
        'date' => now()->format('Y-m-d'),
        'cache_hits' => 0,
        'cache_misses' => 0,
        'total_file_size' => 0,
        'images_ocrd_count' => 0,
        'milliseconds_spent_ocring' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
