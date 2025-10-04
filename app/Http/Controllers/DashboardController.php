<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function getClientChartData()
    {
        // Fetch total clients grouped by client_name
        $clients = DB::table('tbl_client')
            ->select('client_name', DB::raw('COUNT(*) as total'))
            ->groupBy('client_name')
            ->orderBy('total', 'desc')
            ->where('status', 1)
            ->get();

        // Prepare data for the chart
        $labels = $clients->pluck('client_name');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getCreatorChartData()
    {
        // Fetch total clients grouped by creator_name
        $clients = DB::table('tbl_creator')
            ->select('creator_name', DB::raw('COUNT(*) as total'))
            ->groupBy('creator_name')
            ->orderBy('total', 'desc')
            ->where('status', 1)
            ->get();

        //  Prepare data for the chart
        $labels = $clients->pluck('creator_name');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getSalespersonChartData()
    {
        // Fetch total clients grouped by person_name
        $clients = DB::table('tbl_salesperson')
            ->select('person_name', DB::raw('COUNT(*) as total'))
            ->groupBy('person_name')
            ->orderBy('total', 'desc')
            ->where('status', 1)
            ->get();

        //  Prepare data for the chart
        $labels = $clients->pluck('person_name');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}