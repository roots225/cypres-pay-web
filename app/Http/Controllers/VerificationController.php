<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->get('code');
        return view('verifier', compact('code'));
    }
}
