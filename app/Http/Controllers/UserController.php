<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use function Laravel\Prompts\password;

class UserController extends Controller
{
    public function getallusers(Request $request)
    {

        $valid = Validator::make($request->all(), [
            // "limit" => "required|numeric",
            // "offset" => "required|numeric"
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $user = new User();
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
            $user = $user->getuser($data);
            // print_r($user);
            // exit();
            if ($user) {
                return response()->json(['status' => 200, 'count' => $user[0], 'data' => $user[1]]);
            } else {
                return response()->json(['status' => 400, 'error' => 'Something went wrong.'], 400);
            }
        }
    }

    public function update(Request $request)
    {
        $valid = Validator::make($request->all(), [
            "user_id" => "required",
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            try {
                $user = User::findOrFail($request->input('user_id'));

                if ($request->hasFile('profile_picture')) {
                    $profilePath = $request->file('profile_picture');
                    if ($profilePath->isValid()) {
                        $originalName = $profilePath->getClientOriginalName();
                        $sanitizedFilename = preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                        $uniqueName = $sanitizedFilename . '_' . time() . '.' . $profilePath->getClientOriginalExtension();
                        
                        // Store in project folder medi/userprofile
                        $destinationPath = base_path('medi/userprofile'); 
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
                        
                        $profilePath->move($destinationPath, $uniqueName);
                        
                        // Generate accessible URL (optional)
                        $fullpath = url('medi/userprofile/' . $uniqueName);
                        $user->profile_picture = $fullpath;
                    } else {
                        throw new \Exception('Invalid file uploaded.');
                    }
                }
                

                if ($request->has('name')) {
                    $user->name = $request->input('name');
                }
                if ($request->has('last_name')) {
                    $user->last_name = $request->input('last_name');
                }
                if ($request->has('email')) {
                    $user->email = $request->input('email');
                }
                if ($request->has('mobile_number')) {
                    $user->mobile_number = ltrim(str_replace(' ', '', $request->input('mobile_number')), "0");
                }
                if ($request->has('guid')) {
                    $user->guid = $request->input('guid');
                }
                

                $user->save();

                $updatedUser = User::where('user_id', $request->input('user_id'))->first();
                return response()->json(['status' => 200, 'data' => $updatedUser]);
            } catch (\Exception $e) {
                \Log::error($e);
                return response()->json(['status' => 400, 'error' => "Something went wrong."], 400);
            }
        }
    }

    public function updatePassword(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'user_id' => 'required|exists:tbl_user,user_id',
            'password' => 'required|min:6',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 400, 'error' => $valid->errors()], 400);
        } else {
            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User not found'
                ]);
            }

            $user->password = Hash::make($request->password);
            $user->save();
        }
        return response()->json(['status' => 200, 'message' => 'Password updated successfully']);
    }


}
