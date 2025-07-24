<?php

// App\Models\Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{protected $fillable = [
    'title',
    'body',
    'type',
    'student_id',
    'child_id',
    'notifiable_type',
    'notifiable_id',
    'data',
    'read_at',
    'created_at',
    'updated_at'
];


    public function child()
    {
        return $this->belongsTo(Child::class);
    }


// Define the relationships
    public function user()
    {
        return $this->belongsTo(Client::class, 'user_id', 'id');
    }
}
