<?php

namespace App\Services;

use App\ApiUserToken;
use Carbon\Carbon;
use GuzzleHttp\Client;

class ApiUserTokenService
{
    public static function getToken()
    {
        date_default_timezone_set(config('services.inverter.timezone'));

        $apiUserToken = ApiUserToken::first();
        if ($apiUserToken !== null) {
            return $apiUserToken;
        }

        $response = self::getNewToken();

        $createdAt = Carbon::now();
        $expiresAt = $createdAt->clone()->addSeconds($response->expires_in);

        $apiUserToken = new ApiUserToken();
        $apiUserToken->access_token = $response->access_token;
        $apiUserToken->refresh_token = $response->refresh_token;
        $apiUserToken->created_at = $createdAt->format('Y-m-d H:i:s');
        $apiUserToken->expires_at = $expiresAt->format('Y-m-d H:i:s');
        $apiUserToken->save();

        return $apiUserToken;
    }

    private static function getNewToken()
    {
        $guzzleClient = new Client([
            'base_uri' =>  env('API_SERVER_URL'),
        ]);
        $response = $guzzleClient->post('oauth/token', [
            'json' => [
                "grant_type" => "password",
                "client_id" => env('API_SERVER_CLIENT_ID'),
                "client_secret" => env('API_SERVER_CLIENT_SECRET'),
                "username" => env('API_SERVER_USERNAME'),
                "password" => env('API_SERVER_PASSWORD'),
                "scope" => ""
            ]
        ]);

        return json_decode((string) $response->getBody());
    }
}
