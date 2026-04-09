<?php
// app/Models/ChatRoom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'created_by',
        'tipo',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participantes()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_room_id', 'user_id')
            ->withPivot('role', 'ultima_visualizacao')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'desc');
    }

    public function ultimaMensagem()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    public function isAdmin(User $user)
    {
        $participant = $this->participantes()->where('user_id', $user->id)->first();
        return $participant && $participant->role === 'admin';
    }

    public function isParticipant(User $user)
    {
        return $this->participantes()->where('user_id', $user->id)->exists();
    }
}
