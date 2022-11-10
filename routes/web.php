<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\UserController;
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

Route::get('/hello', [HelloWorldController::class, "show"]);
// Route::get('/users/list', [UserController::class, "index"])->middleware("auth", 'can:isAdmin');
// Route::get('/users/edit', [UserController::class, "edit"]);
// // Route::get('/edit', [UserController::class, "edit"]);
// Route::delete('/users/{user}', [UserController::class, "destroy"])->middleware("auth", 'can:isAdmin');

Route::middleware('can:isAdmin')->group(function () {
    Route::resource('users', UserController::class)->only([
        'index', 'edit', 'update', 'destroy',
    ]);
});

Auth::routes();

Route::get('/home', [ExcelController::class, 'index'])->middleware("auth");
Route::post('/import', [ExcelController::class, 'importData'])->middleware("auth", 'can:isAdmin');
Route::get('/export', [ExcelController::class, 'exportData']);
Route::get('/search', [ExcelController::class, 'search']);

Route::get('/raports', [ChartController::class, 'raport'])->middleware("auth");
Route::get('/raports/line', [ChartController::class, 'raportLine']);
