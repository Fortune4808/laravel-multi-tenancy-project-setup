<?php

namespace App\Http\Controllers\v1\Central\Auth;

use Illuminate\Http\Request;
use App\Models\Central\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StaffResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'emailAddress' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = Staff::where('email', $request->emailAddress)->first();
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
        $token = $user->createToken('central_staff_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'accessToken' => $token,
            'tokenType' => 'Bearer'
        ], 200);
    }

    public function sendPasswordResetLink(Request $request)
    {

        $request->validate([
            'emailAddress' => 'required|string|email'
        ]);

        $user = Staff::where('email', $request->emailAddress)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No staff account found with this email address.'
            ], 404);
        }

        if ($user->status_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active. Please contact support.'
            ], 403);
        }

        $status = Password::broker('centralstaff')->sendResetLink([
            'email' => $user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email address.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send password reset link. Please try again later.'
            ], 500);
        }
    }


    public function finishResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'emailAddress' => 'required|string|email|exists:staff,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker('centralstaff')->reset(
            [
                'email' => $request->emailAddress,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        $staffData = Cache::remember("staff_profile_{$user->staff_id}", now()->addMonth(), function () use ($user) {
            return new StaffResource(Staff::with([
                'status:status_id,status_name'
            ])->FindOrFail($user));
        });

        return response()->json([
            'success' => true,
            'data' => $staffData,
        ]);
    }
}
