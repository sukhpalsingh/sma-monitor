<?php

namespace App\Services;

use Carbon\Carbon;

class SmaLogReaderService
{
    private $curl = null;

    private $cookieFilePath = 'storage/sma-log-reader-cookie.txt';

    private $sid = null;

    private $minutesLogCode = 28672;

    private $hourLogCode = 28704;

    /**
     * Performs the inverter login
     */
    public function login()
    {
        if ($this->curl !== null) {
            return;
        }

        $smaInverterUrl = config('services.inverter.url');
        $smaInverterUserName = config('services.inverter.username');
        $smaInverterPassword = config('services.inverter.password');

        $this->curl = curl_init();
        curl_setopt_array($this->curl, [
            CURLOPT_URL => "http://" . $smaInverterUrl . "/dyn/login.json",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => 0,
            CURLOPT_COOKIEJAR => $this->cookieFilePath,
            CURLOPT_COOKIEFILE => $this->cookieFilePath,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json, text/plain, */*',
                'Connection: Keep-Alive',
            ],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => '{"right":"' . $smaInverterUserName . '","pass":"' . $smaInverterPassword . '"}'
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return abort(500, $err);
        }

        $response = json_decode($response, true);
        if (!isset($response['result'])) {
            abort(500, 'Not able to process response');
        }

        $this->sid = $response['result']['sid'] ?? '';
    }

    /**
     * Performs the inverter logout
     */
    public function logout()
    {
        $smaInverterUrl = config('services.inverter.url');
        curl_setopt($this->curl, CURLOPT_URL, "http://{$smaInverterUrl}/dyn/logout.json?sid=" . $this->sid);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, '{}');

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);
        curl_close($this->curl);
    }

    /**
     * Get minutes log
     *
     * @param integer $startTimeStamp
     * @param integer $endTimeStamp
     */
    public function getMinutesLog($startTimeStamp, $endTimeStamp)
    {
        $smaInverterUrl = config('services.inverter.url');
        $smaInverterResultCode = config('services.inverter.result_code');

        curl_setopt($this->curl, CURLOPT_URL, "http://{$smaInverterUrl}/dyn/getLogger.json?sid=" . $this->sid);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, '{"destDev":[],"key":' . $this->minutesLogCode . ',"tStart":' . $startTimeStamp . ',"tEnd":' . $endTimeStamp . '}');

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            $data = $response['result'][$smaInverterResultCode];
            return $data;
        }
    }

    public function getCurrentLog()
    {
        date_default_timezone_set(config('services.inverter.timezone'));

        $start = Carbon::now();
        $start->startOfHour();

        $end = $start->clone()->endOfHour();

        $startTime = $start->getTimestamp();
        $endTime = $end->getTimestamp();

        return $this->getMinutesLog($startTime, $endTime);
    }

    public static function getLastLog()
    {
        $smaLogReaderService = new SmaLogReaderService();
        $smaLogReaderService->login();

        $data = $smaLogReaderService->getCurrentLog();

        $smaLogReaderService->logout();

        return $data;
    }
}
