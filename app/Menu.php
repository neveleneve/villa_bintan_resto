<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_category',
        'name',
        'price',
        'description'
    ];
    protected $dates = ['deleted_at'];
}
