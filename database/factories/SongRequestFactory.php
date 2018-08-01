<?php

use Faker\Generator as Faker;

$factory->define(App\SongRequest::class, function (Faker $faker) {
    return [
        'track' => $faker->title,
        'artist' => $faker->name,
        'votes' => 0,
    ];
});
