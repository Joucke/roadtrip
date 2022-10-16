<?php

use App\Models\Trip;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripUserController;

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

Route::get('/dashboard', function () {
    return view('dashboard', [
        'trip' => Trip::latest('updated_at')->first(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('trips', TripController::class);
Route::post('/trips/{trip}/users', [TripUserController::class, 'store'])->name('trip-users.store');
Route::delete('/trips/{trip}/users/{user}', [TripUserController::class, 'destroy'])->name('trip-users.destroy');
require __DIR__.'/auth.php';
