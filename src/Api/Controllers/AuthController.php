<?php

namespace Sculptor\Agent\Api\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function index()
    {
        return response()->json([]);
    }

    public function login(Request $request)
    {
        $login = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($login)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $user = Auth::user();

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['access_token' => $accessToken]);
    }
}
