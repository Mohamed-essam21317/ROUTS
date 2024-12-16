<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['title', 'message', 'target_role', 'is_read'];
    use HasFactory;

    // The table associated with the model


    // Define the relationships
    public function user()
    {
        return $this->belongsTo(Client::class, 'user_id', 'id');
    }
}
