<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorFeature extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'supervisor_features';

    // Define which attributes are mass assignable
    protected $fillable = [
        'supervisor_id',
        'report_id',
        'reports_handled',
        'emergency_status'
    ];

    // Define the relationships
    public function supervisor()
    {
        return $this->belongsTo(Client::class, 'supervisor_id', 'role_based_id');
    }
}
