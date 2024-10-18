<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Utils\ApiResponse;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::guard('api')->user();

        $user = User::where('id', $user->id)->first();
        if (!$user) {
            return ApiResponse::error('User not found', null, 404);
        }
        return ApiResponse::success('User retrieved', $user);
    }

    /**
     * Complete the registration process
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'mobile' => 'required|numeric|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation error', $validator->errors(), 422);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->role = 'user';
        $user->password = bcrypt($request->password);
        $user->save();

        $token = Auth::guard('api')->login($user);

        return ApiResponse::success('User registered', [
            'token' => $token,
            'expires_in' => 3600,
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation error', $validator->errors(), 422);
        }

        $credential = $request->input('phone');
        $password = $request->input('password');

        $credentials = ['phone' => $credential, 'password' => $password];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', null, 401);
        }
        $user = Auth::guard('api')->user();
        if ($user->status !== 'active') {
            Auth::guard('api')->logout();
            return ApiResponse::error('Account is not active, please contact support', null, 403);
        }
        return ApiResponse::success('Login successful', [
            'token' => $token,
            'expires_in' => 3600,
        ]);
    }
}