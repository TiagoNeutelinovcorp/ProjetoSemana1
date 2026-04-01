<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    protected $model = Livro::class;

    public function definition(): array
    {
        return [
            'isbn' => $this->faker->isbn13(),
            'nome' => $this->faker->sentence(3),
            'bibliografia' => $this->faker->paragraph(),
            'preco' => $this->faker->randomFloat(2, 10, 100),
            'editora_id' => Editora::factory(),
        ];
    }
}
