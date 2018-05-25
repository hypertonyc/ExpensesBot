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

  public function createCategories(Request $request)
  {
    $category_name = $request->input('newcat');

    $category = new ExpenseCategory;
    $category->name = $request->name;
    $category->position = $request->position;
    $category->save();

    return response()->json(['id' => $category->id]);
  }
}
