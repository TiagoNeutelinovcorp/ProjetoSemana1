<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Editora extends Model
{
    use HasFactory;

    protected $table = 'editoras';

    protected $fillable = [
        'nome',
        'logotipo',
    ];

    public function livros()
    {
        return $this->hasMany(Livro::class, 'editora_id');
    }

    public function getLogotipoUrlAttribute()
    {
        return $this->logotipo ? asset('storage/' . $this->logotipo) : null;
    }
}
