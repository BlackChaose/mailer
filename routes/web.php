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

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('admins/mailer','MailingController');
Route::post('/run_mailing','MailingController@run_mailing')->name('run_mailing');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


