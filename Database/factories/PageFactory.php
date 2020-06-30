<?php

use Faker\Generator as Faker;
use Modules\Blog\Models\Page;

$factory->define(Page::class, function (Faker $faker) {
    return [
        'title' => Ybazli\Faker\Facades\Faker::word(),
        'content' => Ybazli\Faker\Facades\Faker::paragraph(),
        'status' => 'publish',
    ];
});
