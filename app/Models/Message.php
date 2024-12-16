<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'messages';

    // Define which attributes are mass assignable
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'message_type',
        'timestamp'
    ];

    // Define the relationships
    public function sender()
    {
        return $this->belongsTo(Client::class, 'sender_id', 'role_based_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Client::class, 'receiver_id', 'role_based_id');
    }
}
