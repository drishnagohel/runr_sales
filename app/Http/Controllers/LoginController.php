<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Login;

class LoginController extends Controller
{
    
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
                ], 422);
                }
                
                // Check user exists
                $user = Login::where('email', $request->email)->first();
                // print_r($user);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check password (plain text for demo)
        if ($user->password !== $request->password) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid password'
            ], 401);
        }

        // Login successful
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user
        ], 200);
    }
}
