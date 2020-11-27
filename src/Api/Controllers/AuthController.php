<?php

namespace Sculptor\Agent\Api\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class AuthController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([]);
    }

    public function login(Request $request): JsonResponse
    {
        $login = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($login)) {
            return response()->json(['message' => 'Invalid Credentials']);
        }

        $user = Auth::user();

        return response()->json(['access_token' => $user->createToken('authToken')->accessToken]);
    }

    public function logged(): JsonResponse
    {
        return response()->json(['authenticated' => Auth::user() != null ]);
    }
}
