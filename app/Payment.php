<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reservation_code',
        'order_id',
        'url',
        'status_code',
        'transaction_status',
    ];
}
