<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\FileJob;
use App\Models\StoredFile;
use Faker\Generator as Faker;

$factory->define(FileJob::class, function (Faker $faker) {
    return [
        'original_name' => $faker->fileName.'.srt',
        'new_extension' => 'srt',
        'error_message' => null,
        'input_stored_file_id' => factory(StoredFile::class)->create()->id,
        'output_stored_file_id' => factory(StoredFile::class)->create()->id,
        'started_at' => now()->subSeconds(4),
        'finished_at' => now()->subSeconds(3),
        'created_at' => now()->subSeconds(5),
        'updated_at' => now(),
    ];
});
