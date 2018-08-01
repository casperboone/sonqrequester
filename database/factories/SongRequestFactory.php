<?php

use Faker\Generator as Faker;

$factory->define(App\SongRequest::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'track' => $faker->title,
        'artist' => $faker->name,
        'votes' => 0,
    ];
});
