<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

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

Route::get('/', [TestController::class, 'printRequest']);
Route::get('/expected_error', [TestController::class, 'expectedError']);
Route::get('/unexpected_error', [TestController::class, 'unexpectedError']);
Route::get('/check_str', [TestController::class, 'checkStr']);
