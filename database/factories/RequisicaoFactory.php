<?php

namespace Database\Factories;

use App\Models\Requisicao;
use App\Models\User;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequisicaoFactory extends Factory
{
    protected $model = Requisicao::class;

    public function definition(): array
    {
        return [
            'codigo' => 'REQ-' . $this->faker->unique()->numberBetween(1000, 9999),
            'user_id' => User::factory(),
            'livro_id' => Livro::factory(),
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(14),
            'status' => 'ativo',
        ];
    }
}
