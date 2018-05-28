<?php

namespace App\Http\Controllers;

use App\Expense;

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
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', Carbon::today())->orderBy('created_at')->get();
    return response()->json(['expenses' => $expenses]);
  }

  public function getWeekExpenses()
  {
    $dt = Carbon::now();
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $dt->StartOfWeek())->orderBy('created_at')->get();
    return response()->json(['expenses' => $expenses]);
  }

  public function getMonthExpenses()
  {
    $dt = Carbon::now();
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $dt->StartOfMonth())->orderBy('created_at')->get();
    return response()->json(['expenses' => $expenses]);
  }
}
