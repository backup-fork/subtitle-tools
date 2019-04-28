<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SubIdxBatch\SubIdxBatch;
use Faker\Generator as Faker;

$factory->define(SubIdxBatch::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'max_files' => $faker->numberBetween(1, 10) * 100,
    ];
});
