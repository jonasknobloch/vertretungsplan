<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.17
 * Time: 16:21
 */

$factory->define(App\ScheduleLesson::class, function (Faker\Generator $faker) {
    return [
        'day' => $faker->unique()->numberBetween(0, 6),
        'class' => $faker->numberBetween(10, 20),
        'index' => $faker->numberBetween()
    ];
});