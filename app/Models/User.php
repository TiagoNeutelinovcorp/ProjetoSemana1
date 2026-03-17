<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];
    protected $appends = ['profile_photo_url'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : null;
    }

    public function isBibliotecario(): bool
    {
        return $this->role === 'bibliotecario';
    }

    public function isCliente(): bool
    {
        return $this->role === 'cliente';
    }

    // ==================== REQUISIÇÕES ====================

    public function requisicoes()
    {
        return $this->hasMany(Requisicao::class);
    }

    public function requisicoesAtivas()
    {
        return $this->requisicoes()->whereIn('status', ['ativo', 'pendente']);
    }

    public function podeRequisitar()
    {
        return $this->requisicoesAtivas()->count() < 3;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function alertasDisponibilidade()
    {
        return $this->hasMany(AlertaDisponibilidade::class);
    }

    public function alertasPendentes()
    {
        return $this->hasMany(AlertaDisponibilidade::class)->where('status', 'pendente');
    }
}
