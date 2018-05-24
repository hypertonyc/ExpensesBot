<?php

namespace App\Http\Controllers;

use App\ExpenseCategory;

use Illuminate\Http\Request;


class CategoryController extends Controller
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
      return view('categories');
  }

  public function getCategories()
  {
    $categories = ExpenseCategory::orderBy('position')->get();
    return response()->json(['categories' => $categories]);
  }
}
