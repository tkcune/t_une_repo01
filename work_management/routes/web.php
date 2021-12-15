<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pa0001Controller;
use App\Http\Controllers\Psbs01Controller;
use App\Http\Controllers\Psji01Controller;
use App\Http\Controllers\Ptcm01Controller;
use App\Http\Controllers\Pslg01Controller;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PsnwController;

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
//リソースコマンドによって生成されたルーティング
Route::resource('psbs01', Psbs01Controller::class);
Route::resource('psji01', Psji01Controller::class);
Route::resource('ptcm01', Ptcm01Controller::class);

Route::get('/',[Pa0001Controller::class,'index'])->name('index');

Route::get('/show/{id}/{id2}',[Psbs01Controller::class,'show'])->name('plbs01.show');

//Pa0001Controllerに関するルーティング
Route::prefix('pa0001')->group(function () {
  Route::get('/log',[Pa0001Controller::class,'log_redirct'])->name('log');
  Route::get('/errormsg',[Pa0001Controller::class,'errormsg'])->name('errormsg');
  Route::get('/count',[Pa0001Controller::class,'count'])->name('count');
  Route::get('/count/narrowdown',[Pa0001Controller::class,'count2'])->name('count2');
  Route::get('/count/top',[Pa0001Controller::class,'count3'])->name('count3');
  Route::get('/count/search/department',[Pa0001Controller::class,'count4'])->name('count4');
  Route::get('/count/search/personnel',[Pa0001Controller::class,'count5'])->name('count5');
  Route::get('/clipboard/{id}',[Pa0001Controller::class,'clipboard'])->name('clipboard');
  Route::get('/deleteclipboard',[Pa0001Controller::class,'deleteclipboard'])->name('deleteclipboard');
  Route::get('/redirect',[Pa0001Controller::class,'redirect'])->name('redirect');
  Route::get('/bs/top',[Pa0001Controller::class,'top'])->name('top');
});

//Psbs01Controllerに関するルーティング
Route::prefix('psbs0001')->group(function () {
  Route::post('/bscopy',[Psbs01Controller::class,'copy'])->name('psbs01.copy');
  Route::post('/bssearch/{id}/{id2}',[Psbs01Controller::class,'search'])->name('psbs01.search');
  Route::patch('/update',[Psbs01Controller::class,'update'])->name('psbs01.update');
  Route::patch('/{id}',[Psbs01Controller::class,'hierarchyUpdate'])->name('psbs01.hierarchyUpdate');
  Route::post('/delete/{id}/{id2}',[Psbs01Controller::class,'delete'])->name('psbs01.delete');
});

//Psji01Controllerに関するルーティング
Route::prefix('psji01')->group(function () {
  Route::post('/jicopy',[Psji01Controller::class,'copy'])->name('psji01.copy');
  Route::post('/jisearch/{id}/{id2}',[Psji01Controller::class,'search'])->name('psji01.search');
  Route::post('/jidestroy/{id}/{id2}',[Psji01Controller::class,'destroy'])->name('psji01.destroy');
});

//Ptcm01Controllerに関するルーティング
Route::post('ptcm01/ptcmdelete/{id}/{id2}',[Ptcm01Controller::class,'delete'])->name('ptcm01.delete');

Route::get('/pslg', [Pslg01Controller::class,'index'])->name('test');
Route::post('/create', [Pslg01Controller::class,'create'])->name('pslg01.create');
Route::post('/pslg', [Pslg01Controller::class,'select'])->name('pslg01.select');
Route::post('/pslg/download', [Pslg01Controller::class,'download'])->name('pslg01.download');

Route::get('/psnw01',[PsnwController::class,'index'])->name('psnw01.index');
Route::post('/psnw01',[PsnwController::class,'create'])->name('psnw01.create');





