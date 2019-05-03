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
    $isSub = $faker->boolean;

    $extension = $isSub ? '.sub' : '.idx';

    return [
        'id' => $uuid = $faker->uuid,
        'is_sub' => $isSub,
        'original_name' => Str::snake($faker->sentence).$extension,
        'hash' => $faker->sha1,
        'storage_file_path' => $uuid.$extension,
    ];
});


$factory->define(SubIdxBatchFile::class, function (Faker $faker) {
    return [
        'id' => $uuid = $faker->uuid,
        'sub_original_name' => Str::snake($faker->sentence).'.sub',
        'idx_original_name' => Str::snake($faker->sentence).'.sub',
        'sub_hash' => $faker->sha1,
        'idx_hash' => $faker->sha1,
        'sub_storage_file_path' => $uuid.'/a.sub',
        'idx_storage_file_path' => $uuid.'/a.idx',
    ];
});
