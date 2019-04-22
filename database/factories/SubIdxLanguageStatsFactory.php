<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SubIdxLanguageStats;
use Faker\Generator as Faker;

$factory->define(SubIdxLanguageStats::class, function (Faker $faker) {
    return [
        // 'language' => '',
        'times_seen' => 0,
        'times_extracted' => 0,
        'times_failed' => 0,
    ];
});
