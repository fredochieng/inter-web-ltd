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
Route::any('/users/store', 'UserController@store');
Route::any('/users/approve', 'UserController@approveClient');
Route::any('/customer/{id}', 'UserController@show');
Route::any('/client/{id}/edit', 'UserController@edit');
Route::any('profile', 'UserController@getUserProfile');
Route::any('update-profile/{user}', 'UserController@updateUserProfile');
Route::resource('/users/secretaries', 'SecretaryController');
Route::resource('/users/blacklist', 'BlacklistController');
Route::resource('/users/restrict', 'ReferalsController');
Route::any('/users/commission_restrictions', 'ReferalsController@getRestrictions');

Route::any('/accounts/get_accounts', 'AccountController@get_accounts');

Route::resource('/investments', 'InvestmentController');
Route::any('/investment/approve', 'InvestmentController@approve');
Route::any('/investment/terminate', 'InvestmentController@terminateInvestment');
Route::any('/investment/change_plan', 'InvestmentController@changePlan');

Route::resource('/payment_modes', 'PaymentModeController');
Route::resource('/payments', 'PaymentController');
Route::any('/payments/client/search', 'PaymentController@SearchClient');

Route::resource('/topups', 'TopupController');
Route::any('/topup/approve', 'TopupController@approve');
Route::resource('/terminations', 'TerminationsController');

Route::any('/reports/customer', 'ReportController@customerReport');
Route::any('/reports/investment', 'ReportController@investmentReport');

Route::any('/reports/due-payments', 'ReportController@duePaymentsReport');
Route::any('/reports/view', 'ReportController@showDuePaymentsReports');
Route::any('/report/excel/generate', 'ReportController@downloadExcel');

Route::get('/test/{id}', 'PaymentController@test');

Route::resource('/system/settings', 'SystemController');
Route::any('/system/logs', 'SystemController@getLogs');
Route::any('/system/import', 'SystemController@getImport');

Route::get('/users/export', 'InvestmentController@export');

Route::any('/transactions/summary', 'HomeController@summaryJob');

// New routes for client registration and client invesments

Route::resource('/clients', 'ClientController');
Route::any('/client/add', 'ClientController@create');
Route::any('/client/store', 'ClientController@store');
Route::any('/client/update_referral', 'ReferalsController@updateUserReferal');
Route::any('/client/update_payment_mode', 'PaymentModeController@updateUserPaymentMode');