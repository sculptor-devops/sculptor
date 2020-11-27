<?php

namespace Sculptor\Agent\Api\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sculptor\Agent\Facades\Logs;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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

        return response(['access_token' => $user->createToken('authToken')->accessToken]);
    }
}
