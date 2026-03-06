<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@biblioteca.com',
                'password' => Hash::make('admin123'),
                'role' => 'bibliotecario',
            ]);

            $this->command->info('Administrador criado com sucesso!');
        }
    }
}
