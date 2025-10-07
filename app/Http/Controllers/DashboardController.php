<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{

    /**
 * 🔁 Reusable helper function to apply date filters
 */
    private function applyDateFilter($query, $filter, $column = 'created_at')
    {
        $today = now();

        switch ($filter) {
            case 'today':
                $query->whereDate($column, $today);
                break;

            case 'yesterday':
                $query->whereDate($column, $today->copy()->subDay());
                break;

            case 'thisweek':
                $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]);
                break;

            case 'last7days':
                $query->whereBetween($column, [now()->subDays(6)->startOfDay(), now()->endOfDay()]);
                break;

            case 'lastweek':
                $query->whereBetween($column, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                break;

            case 'thismonth':
                $query->whereMonth($column, now()->month)->whereYear($column, now()->year);
                break;

            case 'last28days':
                $query->whereBetween($column, [now()->subDays(27)->startOfDay(), now()->endOfDay()]);
                break;

            case 'lastmonth':
                $query->whereMonth($column, now()->subMonth()->month)
                    ->whereYear($column, now()->subMonth()->year);
                break;

            case 'thisyear':
                $query->whereYear($column, now()->year);
                break;

            case 'lastyear':
                $query->whereYear($column, now()->subYear()->year);
                break;
        }

        return $query;
    }


    public function getClientChartData(Request $request)
    {
        $filter = $request->input('filter'); 

        $query = DB::table('tbl_client')
            ->select('client_name', DB::raw('COUNT(*) as total'))
            ->where('status', 1);

        // 🕒 Apply Date Filter
        if ($filter) {
            $query = $this->applyDateFilter($query, $filter, 'created_at');
        }

        $clients = $query
            ->groupBy('client_name')
            ->orderBy('total', 'desc')
            ->get();


        // Prepare data for the chart
        $labels = $clients->pluck('client_name');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getCreatorChartData(Request $request)
    {
        $filter = $request->input('filter');

        $query = DB::table('tbl_creator')
            ->select('creator_name', DB::raw('COUNT(*) as total'))
            ->where('status', 1);

        // 🕒 Apply Date Filter
        if ($filter) {
            $query = $this->applyDateFilter($query, $filter, 'created_at');
        }

        $clients = $query
            ->groupBy('creator_name')
            ->orderBy('total', 'desc')
            ->get();

        //  Prepare data for the chart
        $labels = $clients->pluck('creator_name');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getSalespersonChartData(Request $request)
    {
        $filter = $request->input('filter'); 

        $query = DB::table('tbl_salesperson')
            ->select('person_name', DB::raw('COUNT(*) as total'))
            ->where('status', 1);

        // 🕒 Apply Date Filter
        if ($filter) {
            $query = $this->applyDateFilter($query, $filter, 'created_at');
        }

        $clients = $query
            ->groupBy('person_name')
            ->orderBy('total', 'desc')
            ->get();

        //  Prepare data for the chart
        $labels = $clients->pluck('person_name');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getSalesmanagerChartData(Request $request)
    {
        $filter = $request->input('filter');

        $query = DB::table('tbl_salesmanger')
            ->select('salesmanger_name', DB::raw('COUNT(*) as total'))
            ->where('status', 1);

        // 🕒 Apply Date Filter
        if ($filter) {
            $query = $this->applyDateFilter($query, $filter, 'created_at');
        }

        $clients = $query
            ->groupBy('salesmanger_name')
            ->orderBy('total', 'desc')
            ->get();

        //  Prepare data for the chart
        $labels = $clients->pluck('salesmanger_name');
        $data = $clients->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }


    // public function getSalesChartData(Request $request)
    // {
    //     $filter = $request->input('filter'); 

    //     $query = DB::table('tbl_sales_details')
    //         ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
    //         ->where('status', 1);

    //     // Apply date filter if provided
    //     if ($filter) {
    //         $query = $this->applyDateFilter($query, $filter, 'created_at');
    //     }

    //     // Group by date and get count
    //     $sales = $query
    //         ->groupBy(DB::raw('DATE(created_at)'))
    //         ->orderBy(DB::raw('DATE(created_at)'), 'asc')
    //         ->get();

    //     return response()->json([
    //         'labels' => $sales->pluck('date'), // Dates as labels
    //         'data' => $sales->pluck('total'),  // Count of sales per date
    //     ]);
    // }


    public function getSalesChartData(Request $request)
    {
        // $filter = $request->input('filter'); 

        // $query = DB::table('tbl_sales_details')
        //     ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
        //     ->where('status', 1);

        $filter = $request->input('filter');  
        $clientId = $request->input('client_id');
        $creatorId = $request->input('creator_id');
        $salespersonId = $request->input('person_id');
        $salesmanagerId = $request->input('salesmanger_id');
        $salesDate = $request->input('sales_date');
    
        $query = DB::table('tbl_sales_details as sales')
            ->select(DB::raw('DATE(sales.created_at) as date'), DB::raw('COUNT(*) as total'))
            ->where('sales.status', 1)
            ->leftJoin('tbl_client as c', 'sales.client', '=', 'c.client_id')
            ->leftJoin('tbl_creator as cr', 'sales.creator', '=', 'cr.creator_id')
            ->leftJoin('tbl_salesperson as sp', 'sales.sales_person', '=', 'sp.person_id')
            ->leftJoin('tbl_salesmanger as sm', 'sales.smm', '=', 'sm.salesmanger_id');
    

        // Apply date filter if provided
        if ($filter) {
            $query = $this->applyDateFilter($query, $filter, 'created_at');
        }
        if ($salesDate) {
            $query->whereDate('sales.sales_date', $salesDate);
        }
        if ($clientId) {
            $query->where('sales.client', $clientId);
        }
        if ($creatorId) {
            $query->where('sales.creator', $creatorId);
        }
        if ($salespersonId) {
            $query->where('sales.sales_person', $salespersonId);
        }
        if ($salesmanagerId) {
            $query->where('sales.smm', $salesmanagerId);
        }

        // Group by date and get count
        $sales = $query
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'), 'asc')
            ->get();

        return response()->json([
            'labels' => $sales->pluck('date'), // Dates as labels
            'data' => $sales->pluck('total'),  // Count of sales per date
        ]);
    }
}