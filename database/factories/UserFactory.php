<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'is_admin' => false,
        'remember_token' => Str::random(10),
        'email_verified_at' => now(),
        'last_seen_at' => now(),
        'batch_tokens_left' => 100,
        'batch_tokens_used' => $faker->randomElement([0, 0, 0, 10, 15, 100]),
    ];
});
