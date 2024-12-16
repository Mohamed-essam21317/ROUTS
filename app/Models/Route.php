<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'route_name',
        'start_location',
        'end_location',
        'optimized_path',
    ];

    // Relationship with Buses - A route can have many buses
    public function buses()
    {
        return $this->hasMany(Bus::class, 'route_id', 'id');
    }
}
