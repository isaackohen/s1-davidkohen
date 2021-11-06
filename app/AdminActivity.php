<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Jenssegers\Mongodb\Eloquent\Model;

class AdminActivity extends Model
{
    use CrudTrait;

    protected $collection = 'admin_activities';
    protected $connection = 'mongodb';

    protected $fillable = [
        'user', 'type', 'data', 'time',
    ];
}
