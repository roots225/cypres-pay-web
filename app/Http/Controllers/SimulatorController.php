<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulatorController extends Controller
{
    public function index(Request $request)
    {
        $amount = (int) $request->get('montant', 150000);
        return view('simulator', compact('amount'));
    }
}
