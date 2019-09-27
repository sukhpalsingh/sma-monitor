<?php

namespace App\Services;

use App\WeatherLog;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class WeatherService
{

    public static function getCurrentWeather()
    {
        if (env('ENVIRONMENT') !== 'prod') {
            return;
        }

        date_default_timezone_set(config('services.inverter.timezone'));

        $apiKey = config('services.weather.key');
        $locationKey = config('services.weather.location_key');
        $guzzleClient = new Client([
            'base_uri' =>  config('services.weather.url'),
        ]);

        $url = '/currentconditions/v1/' . $locationKey;
        $response = $guzzleClient->request('GET', $url, [
            'query' => [
                'apikey' => $apiKey,
                'details' => 'true'
            ]
        ]);

        if ($response->getStatusCode() === 401) {
            return [];
        }

        $locationsWeather = json_decode($response->getBody()->getContents(), true);
        $weatherData = [];
        foreach ($locationsWeather as $locationWeather) {
            WeatherLog::create([
                'temperature' => $locationWeather['Temperature']['Metric']['Value'], // celcius
                'real_feel_temperature' => $locationWeather['RealFeelTemperature']['Metric']['Value'], // celcius
                'weather_icon' => $locationWeather['WeatherIcon'],
                'humidity' => $locationWeather['RelativeHumidity'],
                'wind_speed' => $locationWeather['Wind']['Speed']['Metric']['Value'], // km/h,
                'uv_index' => $locationWeather['UVIndex'],
                'cloud_cover' => $locationWeather['CloudCover'], // %
                'last_hour_rain' => $locationWeather['Precip1hr']['Metric']['Value'], // mm
                'is_day' => $locationWeather['IsDayTime'],
                'recorded_at' => new Carbon($locationWeather['LocalObservationDateTime']),
            ]);
        }

        return $weatherData;
    }

    public static function getForecastWeather()
    {
        date_default_timezone_set(config('services.inverter.timezone'));

        $apiKey = config('services.weather.key');
        $locationKey = config('services.weather.location_key');
        $guzzleClient = new Client([
            'base_uri' =>  config('services.weather.url'),
        ]);

        $url = '/forecasts/v1/hourly/12hour/' . $locationKey;
        $response = $guzzleClient->request('GET', $url, [
            'query' => [
                'apikey' => $apiKey,
                'details' => 'true',
                'metric' => 'true'
            ]
        ]);

        if ($response->getStatusCode() === 401) {
            return [];
        }

        $locationsWeather = json_decode($response->getBody()->getContents(), true);

        $weatherData = [];
        foreach ($locationsWeather as $locationWeather) {
            $weatherData[] = [
                'recorded_at' => $locationsWeather['DateTime'],
                'temperature' => $locationWeather['Temperature']['Value'], // celcius
                'weather_icon' => $locationWeather['WeatherIcon'],
                'real_feel_temperature' => $locationWeather['RealFeelTemperature']['Value'], // celcius
                'humidity' => $locationWeather['RelativeHumidity'],
                'wind_speed' => $locationWeather['Wind']['Speed']['Value'], // km/h,
                'uv_index' => $locationWeather['UVIndex'],
                'cloud_cover' => $locationWeather['CloudCover'], // %
                'last_hour_rain' => $locationWeather['TotalLiquid']['Value'], // mm
                'is_day_light' => $locationWeather['IsDaylight']
            ];
        }

        return $weatherData;
    }
}
