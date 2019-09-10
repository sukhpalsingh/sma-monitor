<?php

namespace App\Services;

use GuzzleHttp\Client;

class InverterLogService
{
    /**
     * Send log to the server
     */
    public static function send($logs)
    {
        $apiUserToken = ApiUserTokenService::getToken();

        $client = new Client([
            'base_uri' => env('API_SERVER_URL'),
        ]);

        $response = $client->post('/api/inverter-log', [
            'form_params' => [
                'logs' => $logs
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $apiUserToken->access_token,
            ]
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
