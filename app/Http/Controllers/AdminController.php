<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


/**
 * @group Admin Authentication
 * 
 */
class AdminController extends Controller
{
    

    /**
     * Login Admin
     * 
     * @bodyParam email string required Admin Email. Example: admin@mail.com
     * @bodyParam password string required Admin Password. Example: admin-secret
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

        $token = Auth::guard('admin')->attempt($credentials);

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
     * Register Admin
     * 
     * @bodyParam fullname string required Admin Fullname. Example: John Admin
     * @bodyParam email string required Admin Email. Example: admin@mail.com
     * @bodyParam password string required User Password. Example: admin-secret
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
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6',
        ]);

        $user = Admin::create([
            'name' => $reequest->name,
            'email' => $reequest->email,
            'password' => Hash::make($reequest->password),
        ]);


        return response()->json([
            'status' => "success",
            'message' => "Admin Created Successfully"
        ], status: 201);
    }
}
