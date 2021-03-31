<?php

namespace Modules\Blog\Database\factories;

use Ybazli\Faker\Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Blog\Models\Category;

class CategoryFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = new Faker;

        return [
            'name' => $faker->word()
        ];
    }
}
