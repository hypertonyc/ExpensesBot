<?php

namespace App\Http\Controllers;

use App\Expense;

use Illuminate\Http\Request;

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

  public function getExpenses()
  {
    $expenses = Expense::orderBy('created_at')->get();
    return response()->json(['expenses' => $expenses]);
  }
}
