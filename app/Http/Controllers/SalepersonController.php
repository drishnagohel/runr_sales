<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Saleperson;

class SalepersonController extends Controller
{
    public function addsalesperson(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "person_name" => "required",
            "person_mobile" => "required|digits:10",
            "person_email" => "required|email|unique:tbl_salesperson,person_email"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {

            $salesperson = new Saleperson();
            $salesperson->person_name = $request->input('person_name');
            $salesperson->person_mobile = $request->input('person_mobile');
            $salesperson->person_email = $request->input('person_email');
            $salesperson->created_by = $request->input('created_by');
            $salesperson->guid = generateAccessToken(20);

            $result = $salesperson->save();
            $salespersondata = $salesperson->latest()->first();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $salespersondata, "message" => "Saleperson Added Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function getallsalesperson(Request $request)
    {

        $valid = Validator::make($request->all(), [
            // "limit" => "required|numeric",
            // "offset" => "required|numeric"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $salesperson = new Saleperson();
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
            if ($request->has('person_id') && $request->input('person_id') != "") {
                $data['person_id'] = $request->input('person_id');
            }

            if ($request->has('sortby') && $request->input('sortby') != "") {
                $data['sortby'] = $request->input('sortby');
            }
            if ($request->has('sorttype') && $request->input('sorttype') != "") {
                $data['sorttype'] = $request->input('sorttype');
            }
            $salesperson = $salesperson->getsalesperson($data);
            // print_r($salesperson);
            // exit();
            if ($salesperson) {
                return response()->json(['status' => 200, 'count' => $salesperson[0], 'data' => $salesperson[1]]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }

    public function updatesalesperson(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "person_id" => "required",

        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $salesperson = new Saleperson();
            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $request->request->add(['updated_by' =>  $request->input('updated_by')]);

            $newrequest = $request->except(['person_id', 'user_id', 'guid']);

            $result = $salesperson->where('person_id', $request->input('person_id'))->update($newrequest);

            if ($result) {
                return response()->json(['status' => 200, 'message' => "Saleperson Updated Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function deletesalesperson(Request $request)
    {

        $valid = Validator::make($request->all(), [
            "person_id" => "required"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $salesperson = new Saleperson();
            $request->request->add(['status' => '0']);

            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $newrequest = $request->except(['person_id']);
            $result = $salesperson->where('person_id', $request->input('person_id'))->update($newrequest);
            if ($result) {
                return response()->json(['status' => 200, 'data' => "Saleperson deleted successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }
}
