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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/customers', 'UserController');
Route::any('/phones/get_numbers', 'UserController@get_numbers');
Route::any('/users/create', 'UserController@create');
Route::any('/users/approve', 'UserController@approveClient');
Route::any('/customer/{id}', 'UserController@show');

Route::any('/accounts/get_accounts', 'AccountController@get_accounts');

Route::resource('/investments', 'InvestmentController');

Route::resource('/payments', 'PaymentController');

Route::any('/reports/customer', 'ReportController@customerReport');
Route::any('/reports/investment', 'ReportController@investmentReport');

Route::resource('/system/settings', 'SystemController');
Route::any('/system/logs', 'SystemController@showLogs');
Route::post('/system/logs', 'SystemController@postRegister')->name('business.postRegister');

