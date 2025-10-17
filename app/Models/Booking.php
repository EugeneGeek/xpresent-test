<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    protected $fillable = [
        'service_id', 'client_name', 'client_phone',
        'date', 'start_time', 'end_time'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
