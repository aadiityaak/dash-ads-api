<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'is_array',
    ];

    protected $casts = [
        'is_array' => 'boolean',
    ];

    //simpan setting
    public static function set($key, $value)
    {
        if (is_array($value)) {
            $value      = json_encode($value);
            $is_array   = true;
        } else {
            $is_array = false;
        }

        //createorupdate by key
        $setting = self::updateOrCreate(
            ['key'       => $key],
            [
                'value'     => $value,
                'is_array'  => $is_array
            ]
        );
        return $setting;
    }

    //ambil setting
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            if ($setting->is_array) {
                return json_decode($setting->value, true);
            } else {
                return $setting->value;
            }
        } else {
            return $default;
        }
    }
}
