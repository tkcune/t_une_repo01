<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pa0001Controller;
use App\Http\Controllers\Psbs01Controller;
use App\Http\Controllers\Psji01Controller;
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
Route::resource('psbs01', Psbs01Controller::class);

Route::resource('psji01', Psji01Controller::class);

Route::get('/',[Pa0001Controller::class,'index'])->name('index');

Route::patch('/',[Psbs01Controller::class,'update'])->name('psbs01.update');

Route::post('/{id}/{id2}',[Psbs01Controller::class,'delete'])->name('psbs01.delete');



