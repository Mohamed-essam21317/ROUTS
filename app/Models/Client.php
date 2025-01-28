<?php

namespace App\Models;

use  Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'role_based_id',
        'name',
        'email',
        'password',
        'role',
        'phone_number',

    ];

    // Relationship with Students - A parent can have many students
    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id', 'id');
    }

    // Relationship with Buses - A driver can have many buses
    public function busesAsDriver()
    {
        return $this->hasMany(Bus::class, 'driver_id', 'role_based_id');
    }

    // Relationship with Buses - A supervisor can supervise many buses
    public function busesAsSupervisor()
    {
        return $this->hasMany(Bus::class, 'supervisor_id', 'role_based_id');
    }

    // Hash passwords automatically when creating or updating clients
    public static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (isset($client->password)) {
                $client->password = bcrypt($client->password);
            }
        });

        static::updating(function ($client) {
            if ($client->isDirty('password')) {
                $client->password = bcrypt($client->password);
            }
        });
    }
}
