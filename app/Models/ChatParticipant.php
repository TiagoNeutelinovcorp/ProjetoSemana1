<?php
// app/Models/ChatParticipant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'ultima_visualizacao'
    ];

    protected $casts = [
        'ultima_visualizacao' => 'datetime',
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
