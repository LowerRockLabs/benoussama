<?php

use App\Models\Link;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {

    // dd(
    //     Link::whereId(2187)->first()->orders,
    //     // Order::where('link_id', 2187)->get()
    // );

    return view('dashboard');
})->name('dashboard');
