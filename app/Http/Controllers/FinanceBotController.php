<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FinanceBotController extends Controller
{
    public function getUpdate(Request $request)
    {
      Log::debug('New update');
      Log::debug($request->all());
    }
}
