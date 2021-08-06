<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pa0001Controller;
use App\Http\Controllers\Psbs01Controller;
use App\Http\Controllers\LogController;

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

//Route::get('/', function () {
  //  return view('welcome');
//});

Route::get('/',[Pa0001Controller::class,'index'])->name('index');

Route::get('/psbs01/psbs01',[Psbs01Controller::class,'index'])->name('psbs01.index');

Route::post('/psbs01/psbs01',[Psbs01Controller::class,'index'])->name('psbs01.create');


