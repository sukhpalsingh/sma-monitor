<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $logs = InverterLog::where('recorded_at', '>=', Carbon::today()->startOfDay()->format('Y-m-d H:i:s'))
            ->get();

        $first = 0;
        $data = [];
        foreach ($logs as $log) {
            if ($first === 0) {
                $first = $log['total_yield'];
                continue;
            }

            // $data[] = [
            //     'value' => ($log['total_yield'] - $first),
            //     'time' => $log['recorded_at']->format('H:i')
            // ];
            $data[] = ($log['total_yield'] - $first) / 1000;

            $first = $log['total_yield'];
        }
        
        return view('inverter-logs', ['logs' => $data]);
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
