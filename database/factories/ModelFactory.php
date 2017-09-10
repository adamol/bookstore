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
    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'price' => 1000,
        'category_id' => function() {
            return factory(App\Category::class)->create()->id;
        },
        'author_id' => function() {
            return factory(App\Author::class)->create()->id;
        },
    ];
});

$factory->define(App\Author::class, function (Faker\Generator $faker) {
    return [
        'name' => 'John Doe'
    ];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {
    return [
        'name' => 'fantasi'
    ];
});

$factory->define(App\InventoryItem::class, function (Faker\Generator $faker) {
    return [
        'book_id' => 1
    ];
});
