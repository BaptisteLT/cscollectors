<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LogOutController;
use App\Http\Controllers\Auth\SteamAuthController;
use App\Http\Controllers\InventoryRefreshController;

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

Route::get('/', [HomeController::class, 'home'])->name('homepage');
Route::post('/', [HomeController::class, 'home'])->name('homepage');

//Controlleurs invokable
Route::get('/login', SteamAuthController::class)->name('login');
Route::get('/refresh-inventory', InventoryRefreshController::class)->name('refresh-inventory');
//Route de controlleur
Route::get('/logout', [LogOutController::class, 'logout'])->name('logout');

