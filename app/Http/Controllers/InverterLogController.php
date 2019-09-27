<?php

namespace App\Http\Controllers;

use App\Http\Requests\InverterLogRequest;
use App\InverterLog;
use App\WeatherLog;
use App\WeatherPredictionLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InverterLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InverterLogRequest $request)
    {
        date_default_timezone_set(config('services.inverter.timezone'));

        if ($request->has('from')) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay();
            $endDate = $startDate->clone()->endOfDay();
            $totalHours = 24;
        } else {
            $totalHours = Carbon::now()->startOfHour()->format('H');
            $startDate = Carbon::today()->startOfDay();
            $endDate = $startDate->clone()->endOfDay();
        }

        $dailyLogs = $this->getDailyLogs($startDate, $totalHours);

        // retrieve next and previous dates
        $nextDate = null;
        $previousDate = $startDate->clone()->subDay()->format('d-m-Y');
        if ($endDate->lessThan(Carbon::today()->endOfDay())) {
            $nextDate = $endDate->clone()->addDay()->format('d-m-Y');
        }

        $currentWeather = WeatherLog::where('recorded_at', '>=', Carbon::now()->startOfHour()->subHour())
            ->orderBy('recorded_at', 'desc')
            ->first();

        $weatherPredictionLogs = WeatherPredictionLog::where('recorded_at', '>=', Carbon::now()->startOfHour())
            ->orderBy('recorded_at')
            ->limit(9)
            ->get();

        // $currentHour = Carbon::now()->format('H');
        // if ($currentHour <= 5 || $currentHour >= 19) {
        //     $theme = 'night';
        // } elseif ($currentHour <= 7) {
        //     $theme = 'sunrise';
        // } elseif ($currentHour >= 5) {
        //     $theme = 'sunset';
        // } else {
        //     $theme = 'day';
        // }
        $theme = 'day';

        return view(
            'inverter-logs',
            [
                'title' => $startDate->format('d/m/Y') . ' (' . $dailyLogs['total'] . ' KW)',
                'logs' => $dailyLogs['data'],
                'labels' => $dailyLogs['labels'],
                'nextDate' => $nextDate,
                'previousDate' => $previousDate,
                'theme' => $theme,
                'currentWeather' => $currentWeather,
                'weatherPredictionLogs' => $weatherPredictionLogs
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        date_default_timezone_set(config('services.inverter.timezone'));

        foreach ($request->logs as $log) {
            $inverterLog = InverterLog::where('total_yield', $log['v'])
                ->first();
            if ($inverterLog !== null) {
                continue;
            }

            $timestamp = Carbon::createFromTimestamp($log['t']);

            $inverterLog = new InverterLog();
            $inverterLog->total_yield = $log['v'];
            $inverterLog->recorded_at = $timestamp->format('Y-m-d H:i:s');
            $inverterLog->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $inverterLog->save();
        }

        return response()->json('Logs saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    public function show($date)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $dailyLogs = $this->getDailyLogs($startDate, 24);
        return response()->json($dailyLogs);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get daily logs
     */
    private function getDailyLogs($startDate, $totalHours)
    {
        $data = [];
        $labels = [];
        $total = 0;

        // get previous yield for comparison
        $yesterday = InverterLog::where('recorded_at', '<', $startDate)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $first = isset($yesterday) ? $yesterday->total_yield : 0;

        for ($i = 0; $i <= 24; $i++) {
            $labels[] = $startDate->clone()->addHours($i + 1)->format('H');

            if ($i > $totalHours) {
                continue;
            }

            $logs = InverterLog::where('recorded_at', '>=', $startDate->clone()->addHours($i)->format('Y-m-d H:i:s'))
                ->where('recorded_at', '<', $startDate->clone()->addHours($i + 1)->format('Y-m-d H:i:s'))
                ->get();

            $yield = 0;
            foreach ($logs as $index => $log) {
                $yield += $log['total_yield'] - $first;
                $first = $log['total_yield'];
            }

            $total += ($yield / 1000);
            $data[] = $yield / 1000;
        }

        $total = number_format($total, 2);

        return [
            'total' => $total,
            'data' => $data,
            'labels' => $labels
        ];
    }
}
