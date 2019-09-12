<?php

namespace App\Http\Controllers;

use App\Http\Requests\InverterLogRequest;
use App\InverterLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InverterLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InverterLogRequest $request)
    {
        $first = 0;
        $data = [];
        $labels = [];

        date_default_timezone_set(config('services.inverter.timezone'));

        if ($request->has('from')) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay();
            $endDate = $startDate->clone()->endOfDay();
        } else {
            $startDate = Carbon::today()->startOfDay();
            $endDate = $startDate->clone()->endOfDay();
        }

        $nextDate = null;
        $previousDate = $startDate->clone()->subDay()->format('d-m-Y');
        if ($endDate->lessThan(Carbon::today()->endOfDay())) {
            $nextDate = $endDate->clone()->addDay()->format('d-m-Y');
        }

        // get previous yield for comparison
        $yesterday = InverterLog::where('recorded_at', '<', $startDate)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $first = isset($yesterday) ? $yesterday->total_yield : 0;

        $total = 0;
        for ($i = 0; $i <= 24; $i++) {
            $logs = InverterLog::where('recorded_at', '>=', $startDate->clone()->addHour($i)->format('Y-m-d H:i:s'))
                ->where('recorded_at', '<', $startDate->clone()->addHour($i + 1)->format('Y-m-d H:i:s'))
                ->get();

            $yield = 0;
            foreach ($logs as $index => $log) {
                $yield += $log['total_yield'] - $first;
                $first = $log['total_yield'];
            }

            $total += ($yield / 1000);
            $data[] = $yield / 1000;

            $labels[] = $startDate->clone()->addHour($i)->format('H');
        }

        $total = number_format($total, 2);
        return view(
            'inverter-logs',
            [
                'title' => $startDate->format('d/m/Y') . ' (' . $total . ' KW)',
                'logs' => $data,
                'labels' => $labels,
                'nextDate' => $nextDate,
                'previousDate' => $previousDate
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
}
