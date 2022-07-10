<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Integer as TypesInteger;
use Ramsey\Uuid\Type\Integer;

class AdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin'], ['except' => ['login', 'register']]);
    }

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
            'user_type' => 'Admin',
            'password' => Hash::make($reequest->password),
        ]);


        $token = Auth::login($user);

        return response()->json([
            'status' => "success",
            'message' => "User Created Successfully",
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer'
            ]
        ], status: 201);
    }

    


}
