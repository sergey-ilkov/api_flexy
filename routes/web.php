<?php

use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('home.index');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/login', function () {
//     return 'Login';
// })->name('login');