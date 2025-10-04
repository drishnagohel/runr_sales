<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getClientChartData()
    {
        // Group clients by creation date
        $clients = DB::table('tbl_client')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'), 'asc')
            ->get();

        // Format for chart use (pie chart expects labels + data)
        $labels = $clients->pluck('date');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getCreatorChartData()
    {
        // Group clients by creation date
        $clients = DB::table('tbl_creator')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'), 'asc')
            ->get();

        // Format for chart use (pie chart expects labels + data)
        $labels = $clients->pluck('date');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getSalespersonChartData()
    {
        // Group clients by creation date
        $clients = DB::table('tbl_salesperson')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'), 'asc')
            ->get();

        // Format for chart use (pie chart expects labels + data)
        $labels = $clients->pluck('date');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}