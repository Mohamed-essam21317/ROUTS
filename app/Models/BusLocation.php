<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusLocation extends Model
{
    use HasFactory;

    protected $fillable = ['bus_id', 'latitude', 'longitude', 'location_timestamp',];

    // The table associated with the model.
    protected $table = 'bus_locations';
}
