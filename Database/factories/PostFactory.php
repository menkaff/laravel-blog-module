<?php

namespace Modules\Blog\Database\factories;

use Ybazli\Faker\Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Modules\Blog\Models\Post;

class PostFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = new Faker;

        return [
            'title' => $faker->word(),
            'content' => $faker->sentence(),
            'images' =>  [
                UploadedFile::fake()->image('image2.png', 600, 600),
                UploadedFile::fake()->image('image1.png', 600, 600),
                UploadedFile::fake()->image('image4.png', 600, 600),
            ],
        ];
    }
}
