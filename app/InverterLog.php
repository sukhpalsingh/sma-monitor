<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InverterLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recorded_at',
        'total_yield'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    const UPDATED_AT = null;
}
