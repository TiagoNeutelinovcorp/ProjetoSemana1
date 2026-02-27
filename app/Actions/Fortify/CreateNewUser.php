<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'role' => ['required', 'in:cliente,bibliotecario'],
        ];

        // Validação do código secreto para bibliotecário
        if (isset($input['role']) && $input['role'] === 'bibliotecario') {
            $rules['secret_code'] = ['required', function ($attribute, $value, $fail) {
                if ($value !== env('SECRET_CODE_BIBLIOTECARIO', 'biblioteca2025')) {
                    $fail('Código secreto inválido para criar conta de bibliotecário.');
                }
            }];
        }

        Validator::make($input, $rules)->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'],
        ]);
    }
}
