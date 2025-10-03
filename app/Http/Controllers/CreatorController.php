<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Creator;

class CreatorController extends Controller
{
    public function addcreator(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "creator_name" => "required",
            "creator_mobile" => "required|digits:10",
            "creator_email" => "required|email|unique:tbl_creator,creator_email"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {

            $creator = new Creator();
            $creator->creator_name = $request->input('creator_name');
            $creator->creator_mobile = $request->input('creator_mobile');
            $creator->creator_email = $request->input('creator_mobile');
            $creator->created_by = $request->input('created_by');
            $creator->guid = generateAccessToken(20);

            $result = $creator->save();
            $creatordata = $creator->latest()->first();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $creatordata, "message" => "Creator Added Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function getallcreator(Request $request)
    {

        $valid = Validator::make($request->all(), [
            // "limit" => "required|numeric",
            // "offset" => "required|numeric"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $creator = new Creator();
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
            if ($request->has('creator_id') && $request->input('creator_id') != "") {
                $data['creator_id'] = $request->input('creator_id');
            }

            if ($request->has('sortby') && $request->input('sortby') != "") {
                $data['sortby'] = $request->input('sortby');
            }
            if ($request->has('sorttype') && $request->input('sorttype') != "") {
                $data['sorttype'] = $request->input('sorttype');
            }
            $creator = $creator->getcreator($data);
            // print_r($creator);
            // exit();
            if ($creator) {
                return response()->json(['status' => 200, 'count' => $creator[0], 'data' => $creator[1]]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }

    public function updatecreator(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "creator_id" => "required",

        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $creator = new Creator();
            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $request->request->add(['updated_by' =>  $request->input('updated_by')]);

            $newrequest = $request->except(['creator_id', 'user_id', 'guid']);

            $result = $creator->where('creator_id', $request->input('creator_id'))->update($newrequest);

            if ($result) {
                return response()->json(['status' => 200, 'message' => "Creator Updated Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function deletecreator(Request $request)
    {

        $valid = Validator::make($request->all(), [
            "creator_id" => "required"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $creator = new Creator();
            $request->request->add(['status' => '0']);

            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $newrequest = $request->except(['creator_id']);
            $result = $creator->where('creator_id', $request->input('creator_id'))->update($newrequest);
            if ($result) {
                return response()->json(['status' => 200, 'data' => "Creator deleted successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }
}
