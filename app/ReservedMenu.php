<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReservedMenu extends Model
{
    protected $fillable = [
        'reservation_code',
        'menu_id',
        'harga',
        'jumlah',
    ];
}
