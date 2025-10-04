<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Sales;

class SalesController extends Controller
{
    public function addsales(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "sales_person" => "required",
            "creator" => "required",
            "smm" => "required"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {

            $sales = new Sales();
            $sales->total_time = $request->input('total_time');
            $sales->link = $request->input('link');
            $sales->sales_date = $request->input('sales_date');
            $sales->sales_person = $request->input('sales_person');
            $sales->creator = $request->input('creator');
            $sales->smm = $request->input('smm');
            $sales->client = $request->input('client');
            $sales->caption = $request->input('caption');
            $sales->created_by = $request->input('created_by');
            $sales->guid = generateAccessToken(20);

            $result = $sales->save();
            $salesdata = $sales->latest()->first();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $salesdata, "message" => "Sales Details Added Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function getallsales(Request $request)
    {

        $valid = Validator::make($request->all(), [
            // "limit" => "required|numeric",
            // "offset" => "required|numeric"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $sales = new Sales();
            $data = array();
            if ($request->has('offset') && $request->filled('offset')) {
                $data["offset"] = $request->input("offset");
            }

            if ($request->has('limit') && $request->filled('limit')) {
                $data["limit"] = $request->input("limit");
            }
            if ($request->has('search') && $request->input('search') != "") {
                $data['search'] = $request->input('search');
            }

            if ($request->has('from_date') && $request->filled('from_date') && $request->has('to_date') && $request->filled('to_date')) {
                $data['from_date'] = $request->input('from_date');
                $data['to_date'] = $request->input('to_date');
            }
            if ($request->has('sales_details_id') && $request->input('sales_details_id') != "") {
                $data['sales_details_id'] = $request->input('sales_details_id');
            }

            if ($request->has('sortby') && $request->input('sortby') != "") {
                $data['sortby'] = $request->input('sortby');
            }
            if ($request->has('sorttype') && $request->input('sorttype') != "") {
                $data['sorttype'] = $request->input('sorttype');
            }
            $sales = $sales->getsales($data);
            // print_r($sales);
            // exit();
            if ($sales) {
                return response()->json(['status' => 200, 'count' => $sales[0], 'data' => $sales[1]]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }

    public function updatesales(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "sales_details_id" => "required",

        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $sales = new Sales();
            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $request->request->add(['updated_by' =>  $request->input('updated_by')]);

            $newrequest = $request->except(['sales_details_id', 'user_id', 'guid']);

            $result = $sales->where('sales_details_id', $request->input('sales_details_id'))->update($newrequest);

            if ($result) {
                return response()->json(['status' => 200, 'message' => "Sales Details Updated Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function deletesales(Request $request)
    {

        $valid = Validator::make($request->all(), [
            "sales_details_id" => "required"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $sales = new Sales();
            $request->request->add(['status' => '0']);

            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $newrequest = $request->except(['sales_details_id']);
            $result = $sales->where('sales_details_id', $request->input('sales_details_id'))->update($newrequest);
            if ($result) {
                return response()->json(['status' => 200, 'data' => "Sales Detail deleted successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }
}
