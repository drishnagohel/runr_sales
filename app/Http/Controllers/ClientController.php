<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function addclient(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "client_name" => "required",
            "client_mobile" => "required|digits:10",
            "client_email" => "required|email|unique:tbl_client,client_email"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {

            $client = new Client();
            $client->client_name = $request->input('client_name');
            $client->client_mobile = $request->input('client_mobile');
            $client->client_email = $request->input('client_email');
            $client->created_by = $request->input('created_by');
            $client->guid = generateAccessToken(20);

            $result = $client->save();
            $client = $client->latest()->first();
            if ($result) {
                return response()->json(['status' => 200, 'data' => $client, "message" => "Client Added Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    // public function getallclientselect2(Request $request){
    //     $search = $request->search;
    //     if($search == ''){
    //         $clientnamelist = Client::orderby('client_name','asc')->select('client_id','client_name')->paginate(8);
    //     } else {
    //         $clientnamelist = Client::orderby('client_name','asc')->select('client_id','client_name')->where('client_name', 'like', '%' .$search . '%')->paginate(8);
    //     }

    //     $response = array();
    //     foreach($clientnamelist as $postcategory){
    //         $response[] = array(
    //             "id" => $postcategory->client_id,
    //             "text" => $postcategory->client_name
    //         );
    //     }
    //     return response()->json($response);
    // }

    public function getallclientselect2(Request $request)
    {
        $search = trim($request->search);
        Log::info("Search term: " . $search);

        // Get matching Clients based on the search term
        $clientlist = Client::orderBy('client_name', 'asc')
            ->select('client_id', 'client_name')
            ->where('client_name', 'like', '%' . $search . '%')
            ->where('status', 1)
            ->limit(100)
            ->get();

        Log::info("Client count: " . $clientlist->count());

        $response = [];

        // Populate response with existing Clients
        foreach ($clientlist as $client) {
            $response[] = [
                "id" => $client->client_id,
                "text" => $client->client_name,
            ];
        }

        // Check if the search term exists in the database
        $existing = Client::whereRaw('LOWER(client_name) = ?', [strtolower($search)])->first();
        Log::info("Does exact '$search' exist? " . ($existing ? 'Yes' : 'No'));

        // If no matching Client exists and the search term is long enough, add a new Client
        if (!$existing && strlen($search) >= 3) {
            $newClient = Client::create([
                'client_name' => $search,
                'guid' => generateAccessToken(20),
            ]);

            $response[] = [
                "id" => $newClient->client_id, // ✅ return correct ID
                "text" => $newClient->client_name,
            ];

            Log::info("Inserted new Client: " . $search);
        }
        Log::info("Response: ", $response);

        return response()->json($response);
    }
    
    public function getallclient(Request $request)
    {

        $valid = Validator::make($request->all(), [
            // "limit" => "required|numeric",
            // "offset" => "required|numeric"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $client = new Client();
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
            if ($request->has('client_id') && $request->input('client_id') != "") {
                $data['client_id'] = $request->input('client_id');
            }
            if ($request->has('client_name') && $request->input('client_name') != "") {
                $data['client_name'] = $request->input('client_name');
            }

            if ($request->has('sortby') && $request->input('sortby') != "") {
                $data['sortby'] = $request->input('sortby');
            }
            if ($request->has('sorttype') && $request->input('sorttype') != "") {
                $data['sorttype'] = $request->input('sorttype');
            }
            $client = $client->getclient($data);
            // print_r($client);
            // exit();
            if ($client) {
                return response()->json(['status' => 200, 'count' => $client[0], 'data' => $client[1]]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }

    public function updateclient(Request $request)
    {
        //validations
        $valid = Validator::make($request->all(), [
            "client_id" => "required",

        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $client = new Client();
            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $request->request->add(['updated_by' =>  $request->input('updated_by')]);

            $newrequest = $request->except(['client_id', 'user_id', 'guid']);

            $result = $client->where('client_id', $request->input('client_id'))->update($newrequest);

            if ($result) {
                return response()->json(['status' => 200, 'message' => "Client Updated Successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function deleteclient(Request $request)
    {

        $valid = Validator::make($request->all(), [
            "client_id" => "required"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $client = new Client();
            $request->request->add(['status' => '0']);

            $request->request->add(['updated_at' => Carbon::now()->toDateTimeString()]);
            $newrequest = $request->except(['client_id']);
            $result = $client->where('client_id', $request->input('client_id'))->update($newrequest);
            if ($result) {
                return response()->json(['status' => 200, 'data' => "Client deleted successfully"]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }
}
