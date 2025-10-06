<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    
    public function login(Request $request)
    {
        // 1️⃣ Validate input
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        $email = trim($request->email);
        $password = trim($request->password);
    
        // 2️⃣ Check if user exists
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            // User does not exist → create new user with MD5 password
            $user = new User();
            $user->email = $email;
            $user->password = md5($password); // Save MD5 hash
            $user->name = $request->input('name', ''); // Optional, default empty
            $user->last_name = $request->input('last_name', '');
            $user->mobile_number = $request->input('mobile_number', '');
            $user->status = 1;
            $user->guid = generateAccessToken(20); // if you have this helper
            $user->save();
    
            return response()->json([
                'status'  => 200,
                'message' => 'User created and logged in successfully',
                'user'    => $user
            ], 200);
        }
    
        // 3️⃣ Check MD5 password
        if ($user->password !== md5($password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid email or password'
            ], 401);
        }
    
        // 4️⃣ Login successful
        return response()->json([
            'status'  => 200,
            'message' => 'Login successful',
            'user'    => $user
        ], 200);
    }
    
}
