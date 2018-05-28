<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/expenses', 'ExpenseController@index')->name('expenses');
Route::get('/api/expenses', 'ExpenseController@getTodayExpenses');
Route::get('/api/expenses/week', 'ExpenseController@getWeekExpenses');
Route::get('/api/expenses/month', 'ExpenseController@getMonthExpenses');

Route::get('/categories', 'CategoryController@index')->name('expense_cats');
Route::get('/api/categories', 'CategoryController@getCategories');
Route::get('/api/categories/create', 'CategoryController@createCategories');

Route::post('/financebot', 'FinanceBotController@getUpdate');
