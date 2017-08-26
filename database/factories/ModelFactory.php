<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Book::class, function (Faker\Generator $faker) {
    $categories = ['fantasi', 'thriller'];

    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'author' => $faker->name,
        'category' => $categories[array_rand($categories)]
    ];
});
