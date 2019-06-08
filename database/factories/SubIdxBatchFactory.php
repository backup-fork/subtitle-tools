<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use App\Models\SubIdxBatch\SubIdxUnlinkedBatchFile;
use Faker\Generator as Faker;

$factory->define(SubIdxBatch::class, function (Faker $faker) {
    static $label = 1;

    return [
        'id' => $faker->uuid,
        'label' => (string) $label++,
        'created_at' => now()->subHours(100 - $label),
        'updated_at' => now(),
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
        'original_name' => $faker->fileName.'.idx',
    ];
});

$factory->state(SubIdxUnlinkedBatchFile::class, 'sub', function (Faker $faker) {
    return [
        'is_sub' => true,
        'original_name' => $faker->fileName.'.sub',
    ];
});


$factory->define(SubIdxBatchFile::class, function (Faker $faker) {
    return [
        'sub_original_name' => $faker->fileName.'.sub',
        'idx_original_name' => $faker->fileName.'.idx',
        'sub_hash' => $faker->sha1,
        'idx_hash' => $faker->sha1,
    ];
});
