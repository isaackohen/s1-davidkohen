<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Jenssegers\Mongodb\Eloquent\Model;

class Currency extends Model
{
    use CrudTrait;

    protected $collection = 'currencies';
    protected $connection = 'mongodb';

    protected $fillable = [
        'currency', 'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
