<?php

use App\Profession;
use Faker\Generator as Faker;

$factory->define(App\UserProfile::class, function (Faker $faker) {
    return [
        'bio'=> $faker->paragraph,
        'twitter'=> 'https://twitter.com/'.$faker->unique()->userName,
        'profession_id' => Profession::all()->random()->id,
    ];
});
