<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Jenssegers\Mongodb\Eloquent\Model;

class Providers extends Model
{
    use CrudTrait;
	
	protected $connection = 'mongodb';
	protected $collection = 'providers';
		
	protected $fillable = [
		'provider',
		'ggr',
		'games',
		'disabled',
		'img'
	];
	
}