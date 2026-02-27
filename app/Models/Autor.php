<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    use HasFactory;

    protected $table = 'autores';

    protected $fillable = [
        'nome',
        'foto',
    ];

    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'autor_livro', 'autor_id', 'livro_id');
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nome) . '&size=100&background=random';
    }
}
