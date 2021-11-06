<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Eloquent\Model;

class Settings extends Model
{
    use CrudTrait;

    protected $connection = 'mongodb';
    protected $collection = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'value', 'cat', 'internal', 'hidden',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public static function get(string $key, string $default = '', bool $hidden = false)
    {
        if (Cache::has('settings:'.$key)) {
            return Cache::get('settings:'.$key);
        }

        $value = self::where('name', $key)->first();
        if (! $value) {
            self::create([
                'name' => $key,
                'value' => $default,
                'hidden' => $hidden,
                'internal' => false,
            ]);

            return $default;
        }

        Cache::put('settings:'.$key, $value->value);

        return $value->value;
    }

    public static function set($key, $value)
    {
        Cache::forget('settings:'.$key);

        $setting = self::where('name', $key);

        if (! $setting->first()) {
            self::create(['name' => $key, 'value' => $value, 'internal' => false, 'hidden' => false]);
        } else {
            $setting->update(['value' => $value]);
        }
    }
}
