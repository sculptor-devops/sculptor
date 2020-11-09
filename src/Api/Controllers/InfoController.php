<?php

namespace Sculptor\Agent\Api\Controllers;

use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    public function index()
    {
        return response()->json(['test']);
    }
}
