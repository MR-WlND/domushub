<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SepayController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('SePay IPN', $request->all());

        return response()->json([
            'success' => true
        ]);
    }
}