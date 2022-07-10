<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @group User Authentication
 * 
 */
class UserController extends Controller
{

    /**
     * Login User
     * 
     * @bodyParam email string required User Email. Example: user@mail.com
     * @bodyParam password string required User Password. Example: secret
     * 
     * @response{
     *  'status' => 'success',
     *       'authorisation' => [
     *          'token' => 'jwt-auth-token',
     *         'type' => 'bearer'
     *    ]
     * }
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Incorrect Login Credentials',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer'
            ]
        ], 200);
    }


    /**
     * Register User
     * 
     * @bodyParam name string required User Full Name. Example: John Doe
     * @bodyParam email string required User Email. Example: admin@mail.com
     * @bodyParam password string required User Password. Example: secret
     * 
     * @response{
     *  'status' => 'success',
     *       'authorisation' => [
     *          'token' => 'jwt-auth-token',
     *         'type' => 'bearer'
     *    ]
     * }
     */

    public function register(Request $reequest)
    {
        $reequest->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $reequest->name,
            'email' => $reequest->email,
            'password' => Hash::make($reequest->password),
        ]);

        return response()->json([
            'status' => "success",
            'message' => "User Created Successfully"
        ], status: 201);
    }
}
