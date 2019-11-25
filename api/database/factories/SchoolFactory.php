<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.17
 * Time: 16:05
 */

$factory->define(App\School::class, function (Faker\Generator $faker) {
    return [
        'key' => $faker->unique()->numberBetween(1000000, 9999999),
        'name' => $faker->unique()->streetName,
    ];
});