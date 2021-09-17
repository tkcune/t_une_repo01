<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pa0001Controller;
use App\Http\Controllers\Psbs01Controller;
use App\Http\Controllers\Psji01Controller;
use App\Http\Controllers\Ptcm01Controller;
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

Route::resource('ptcm01', Ptcm01Controller::class);

Route::get('/',[Pa0001Controller::class,'index'])->name('index');

Route::get('/count',[Pa0001Controller::class,'count'])->name('count');

Route::get('/count2',[Pa0001Controller::class,'count2'])->name('count2');

Route::get('/show/{id}/{id2}',[Psbs01Controller::class,'show'])->name('plbs01.show');

Route::post('/bscopy',[Psbs01Controller::class,'copy'])->name('psbs01.copy');

Route::post('/jicopy',[Psji01Controller::class,'copy'])->name('psji01.copy');

Route::post('/bssearch/{id}',[Psbs01Controller::class,'search'])->name('psbs01.search');

Route::post('/jisearch/{id}',[Psji01Controller::class,'search'])->name('psji01.search');

Route::patch('/',[Psbs01Controller::class,'update'])->name('psbs01.update');

Route::patch('/{id}',[Psbs01Controller::class,'hierarchyUpdate'])->name('psbs01.hierarchyUpdate');

Route::post('/{id}/{id2}',[Psbs01Controller::class,'delete'])->name('psbs01.delete');






