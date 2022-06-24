<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'reservation_code',
        'nama_pemesan',
        'kontak',
        'table_id',
        'time',
        'status',
        'reserved_status',
        'snap_token',
    ];
}
