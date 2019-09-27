<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeatherLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weather_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('temperature', 3, 1);
            $table->float('real_feel_temperature', 3, 1);
            $table->unsignedTinyInteger('weather_icon');
            $table->float('humidity', 3, 1);
            $table->float('wind_speed', 3, 1);
            $table->unsignedTinyInteger('uv_index');
            $table->unsignedTinyInteger('cloud_cover');
            $table->float('last_hour_rain', 3, 1);
            $table->boolean('is_day');
            $table->timestampTz('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weather_logs');
    }
}
