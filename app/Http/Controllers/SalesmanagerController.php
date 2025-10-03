<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Salesmanager;

class SalesmanagerController extends Controller
{
    public function addsalesmanager(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "salesmanger_name" => "required",
            "salesmanger_mobile" => "required|digits:10",
            "salesmanger_email" => "required|email|unique:tbl_salesmanger,salesmanger_email"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {

            $salesmanager = new Salesmanager();
            $salesmanager->salesmanger_name = $request->input('salesmanger_name');
            $salesmanager->salesmanger_mobile = $request->input('salesmanger_mobile');
            $salesmanager->salesmanger_email = $request->input('salesmanger_email');
            $salesmanager->created_by = $request->input('created_by');
            $salesmanager->guid = generateAccessToken(20);

            $result = $salesmanager->save();
            $salesmanagerdata = $salesmanager->latest()->first();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $salesmanagerdata, "message" => "Salesmanager Added Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function getallsalesmanager(Request $request)
    {

        $valid = Validator::make($request->all(), [
            // "limit" => "required|numeric",
            // "offset" => "required|numeric"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $salesmanager = new Salesmanager();
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
            if ($request->has('salesmanger_id') && $request->input('salesmanger_id') != "") {
                $data['salesmanger_id'] = $request->input('salesmanger_id');
            }

            if ($request->has('sortby') && $request->input('sortby') != "") {
                $data['sortby'] = $request->input('sortby');
            }
            if ($request->has('sorttype') && $request->input('sorttype') != "") {
                $data['sorttype'] = $request->input('sorttype');
            }
            $salesmanager = $salesmanager->getsalesmanager($data);
            // print_r($salesmanager);
            // exit();
            if ($salesmanager) {
                return response()->json(['status' => 200, 'count' => $salesmanager[0], 'data' => $salesmanager[1]]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }

    public function updatesalesmanager(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "salesmanger_id" => "required",

        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $salesmanager = new Salesmanager();
            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $request->request->add(['updated_by' =>  $request->input('updated_by')]);

            $newrequest = $request->except(['salesmanger_id', 'user_id', 'guid']);

            $result = $salesmanager->where('salesmanger_id', $request->input('salesmanger_id'))->update($newrequest);

            if ($result) {
                return response()->json(['status' => 200, 'message' => "Salesmanager Updated Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function deletesalesmanager(Request $request)
    {

        $valid = Validator::make($request->all(), [
            "salesmanger_id" => "required"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $salesmanager = new Salesmanager();
            $request->request->add(['status' => '0']);

            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $newrequest = $request->except(['salesmanger_id']);
            $result = $salesmanager->where('salesmanger_id', $request->input('salesmanger_id'))->update($newrequest);
            if ($result) {
                return response()->json(['status' => 200, 'data' => "Salesmanager deleted successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }
}
