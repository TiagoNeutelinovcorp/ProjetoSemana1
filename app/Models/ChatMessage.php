<?php
// app/Models/ChatMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'message',
        'lida',
        'lida_em'
    ];

    protected $casts = [
        'lida' => 'boolean',
        'lida_em' => 'datetime',
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marcarComoLida()
    {
        $this->update([
            'lida' => true,
            'lida_em' => now()
        ]);
    }
}
