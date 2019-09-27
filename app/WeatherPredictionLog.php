<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeatherPredictionLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'temperature',
        'real_feel_temperature',
        'weather_icon',
        'humidity',
        'wind_speed',
        'uv_index',
        'cloud_cover',
        'last_hour_rain',
        'is_day',
        'recorded_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'recorded_at',
    ];
}
