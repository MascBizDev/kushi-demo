<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/charge-token', function () {
    return (new \App\Kushki())->chargeToken();
});

Route::get('/charge', function () {
    return (new \App\Kushki())->charge();
});

Route::get('/subscription-token', function () {
    return (new \App\Kushki())->subscriptionToken();
});

Route::get('/subscription', function () {
    return (new \App\Kushki())->subscription();
});

Route::get('/subscription-cancel', function () {
    return (new \App\Kushki())->cancel();
});


