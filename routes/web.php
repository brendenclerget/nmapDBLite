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

// Authentication routes provided by Laravel
Auth::routes(['register' => false]);

// Page shown upon login
Route::get('/home', 'HomeController@index')->name('home');

//** API Management Routes  **//
Route::get('/api-keys', 'APIController@index')->name('api.index');
//** End API Management Routes  **//

//** Scan Routes  **//
Route::get('/scans', 'ScanController@index')->name('scan.index');
Route::get('/scan/new', 'ScanController@create')->name('scan.create');
Route::get('/scan/{ip}', 'ScanController@show')->name('scan.view');
Route::get('/scan/{scan}/details', 'ScanController@details')->name('scan.details');

Route::post('/scan', 'ScanController@store')->name('scan.store');
//** End Scan Routes  **//
