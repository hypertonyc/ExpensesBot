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
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', Carbon::today())->orderBy('created_at')->get();
    $total_amount = $expenses->sum('amount');
    $categories = collect();
    $expenses->groupBy('category_id')->each(function($item, $key) use (&$categories, $total_amount) {
      $amount = $item->sum('amount');
      $categories->push(array('category' => $item[0]['category'], 'percent' => round($amount / $total_amount * 100),'amount' => round($amount, 2)));
    });
    $categories = $categories->sortByDesc('amount')->values()->all();

    return response()->json(['expenses' => $expenses, 'categories' => $categories, 'total' => round($total_amount, 2)]);
  }

  public function getWeekExpenses()
  {
    $dt = Carbon::now();
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $dt->StartOfWeek())->orderBy('created_at')->get();
    $total_amount = $expenses->sum('amount');
    $categories = collect();
    $expenses->groupBy('category_id')->each(function($item, $key) use (&$categories, $total_amount) {
      $amount = $item->sum('amount');
      $categories->push(array('category' => $item[0]['category'], 'percent' => round($amount / $total_amount * 100),'amount' => round($amount, 2)));
    });
    $categories = $categories->sortByDesc('amount')->values()->all();

    return response()->json(['expenses' => $expenses, 'categories' => $categories, 'total' => round($total_amount, 2)]);
  }

  public function getMonthExpenses()
  {
    $dt = Carbon::now();
    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $dt->StartOfMonth())->orderBy('created_at')->get();
    $total_amount = $expenses->sum('amount');
    $categories = collect();
    $expenses->groupBy('category_id')->each(function($item, $key) use (&$categories, $total_amount) {
      $amount = $item->sum('amount');
      $categories->push(array('category' => $item[0]['category'], 'percent' => round($amount / $total_amount * 100),'amount' => round($amount, 2)));
    });
    $categories = $categories->sortByDesc('amount')->values()->all();

    return response()->json(['expenses' => $expenses, 'categories' => $categories, 'total' => round($total_amount, 2)]);
  }

  public function getPeriodExpenses($from_dt, $to_dt)
  {
    $from_date = Carbon::createFromTimestamp($from_dt);
    $to_date = Carbon::createFromTimestamp($to_dt);

    $expenses = Expense::with(['category', 'user'])->where('created_at','>=', $from_date)->where('created_at','<=', $to_date)->orderBy('created_at')->get();
    $total_amount = $expenses->sum('amount');
    $categories = collect();
    $expenses->groupBy('category_id')->each(function($item, $key) use (&$categories, $total_amount) {
      $amount = $item->sum('amount');
      $categories->push(array('category' => $item[0]['category'], 'percent' => round($amount / $total_amount * 100),'amount' => round($amount, 2)));
    });
    $categories = $categories->sortByDesc('amount')->values()->all();

    return response()->json(['expenses' => $expenses, 'categories' => $categories, 'total' => round($total_amount, 2)]);
  }
}
