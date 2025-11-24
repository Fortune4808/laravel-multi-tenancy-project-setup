<?php

namespace App\Http\Controllers\v1\Branch\Auth;

use Illuminate\Http\Request;
use App\Models\Branch\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'emailAddress' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = Staff::where('email_address', strtolower($request->emailAddress))->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address or password.'
            ], 401);
        }

        if ($user->status_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active. Please contact support.'
            ], 403);
        }

        $user->last_login_at = now();
        $user->save();
        $user->tokens()->delete();
        $token = $user->createToken('branch_staff_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'accessToken' => $token,
            'tokenType' => 'Bearer'
        ], 200);
    }
}
