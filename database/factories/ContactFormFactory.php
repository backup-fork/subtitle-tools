<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\ContactForm;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(ContactForm::class, function (Faker $faker) {
    return [
        'id' => Str::uuid()->toString(),
        'email' => $faker->optional()->email,
        'ip' => $faker->ipv4,
        'message' => $faker->realText(),
        'read_at' => null,
    ];
});
