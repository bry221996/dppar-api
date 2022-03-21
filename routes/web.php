<?php

use App\Models\Personnel;
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


Route::get('/reset/:personnel_id', function ($personnel_id) {
    $personnel = Personnel::where('personnel_id', $personnel_id)->first();

    if ($personnel) {
        $personnel->checkins()->delete();

        return response(['message' => 'Checkins deleted']);
    }

    return response(['message' => 'No personnel found']);
});
