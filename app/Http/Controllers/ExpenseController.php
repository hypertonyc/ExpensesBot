<?php

namespace App\Http\Controllers;

use App\Expense;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      return view('expenses');
  }

  public function getTodayExpenses()
  {
    Log::debug('getTodayExpenses');

    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', Carbon::today())->orderBy('created_at')->get();
    return response()->json(['expenses' => $expenses]);
  }

  public function getWeekExpenses()
  {
    Log::debug('getWeekExpenses');

    $dt = Carbon::now();
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $dt->StartOfWeek())->orderBy('created_at')->get();
    return response()->json(['expenses' => $expenses]);
  }

  public function getMonthExpenses()
  {
    Log::debug('getMonthExpenses');

    $dt = Carbon::now();
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $dt->StartOfMonth())->orderBy('created_at')->get();
    return response()->json(['expenses' => $expenses]);
  }

  public function getPeriodExpenses($from_dt, $to_dt)
  {
    Log::debug('getPeriodExpenses');

    $from_date = Carbon::createFromTimestamp($from_dt);
    $to_date = Carbon::createFromTimestamp($to_dt);

    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $from_date)->where('created_at','<=', $to_date)->orderBy('created_at')->get();

    return response()->json(['expenses' => $expenses]);
  }
}
