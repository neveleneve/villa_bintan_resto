<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReservedFee extends Model
{
    protected $fillable = [
        'reservation_code',
        'fee',
    ];
}
