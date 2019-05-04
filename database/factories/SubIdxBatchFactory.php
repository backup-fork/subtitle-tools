<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use App\Models\SubIdxBatch\SubIdxUnlinkedBatchFile;
use Faker\Generator as Faker;

$factory->define(SubIdxBatch::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'max_files' => $faker->numberBetween(1, 10) * 100,
    ];
});


$factory->define(SubIdxUnlinkedBatchFile::class, function (Faker $faker) {
    return [
        'hash' => $faker->sha1,
    ];
});

$factory->state(SubIdxUnlinkedBatchFile::class, 'idx', function (Faker $faker) {
    return [
        'is_sub' => false,
        'original_name' => Str::snake($faker->sentence).'idx',
    ];
});

$factory->state(SubIdxUnlinkedBatchFile::class, 'sub', function (Faker $faker) {
    return [
        'is_sub' => true,
        'original_name' => Str::snake($faker->sentence).'sub',
    ];
});


$factory->define(SubIdxBatchFile::class, function (Faker $faker) {
    return [
        'sub_original_name' => Str::snake($faker->sentence).'sub',
        'idx_original_name' => Str::snake($faker->sentence).'idx',
        'sub_hash' => $faker->sha1,
        'idx_hash' => $faker->sha1,
    ];
});
