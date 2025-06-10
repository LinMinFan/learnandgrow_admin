<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);

        return [
            'category_id' => $this->faker->randomElement([1, 2, 3, 4]),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph,
            'content' => $this->faker->paragraphs(5, true),
            'cover_image' => null,
            'status' => $this->faker->randomElement(['draft', 'published']),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'author_id' => 1,
            'is_top' => $this->faker->boolean(10),
        ];
    }
}
