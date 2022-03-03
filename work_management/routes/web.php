<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pa0001Controller;
use App\Http\Controllers\Psbs01Controller;
use App\Http\Controllers\Psji01Controller;
use App\Http\Controllers\Ptcm01Controller;
use App\Http\Controllers\Pslg01Controller;
use App\Http\Controllers\LogController;
use App\Http\Controllers\Psnw01Controller;
use App\Http\Controllers\Pppu01Controller;
use App\Http\Controllers\Pskb01Controller;

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

Route::get('/', [Pa0001Controller::class, 'index'])->name('index');

Route::get('/show/{id}/{id2}', [Psbs01Controller::class, 'show'])->name('plbs01.show');

//Pa0001Controllerに関するルーティング
Route::prefix('pa0001')->group(function () {
  Route::get('/log', [Pa0001Controller::class, 'log_redirct'])->name('pa0001.log');
  Route::get('/errormsg', [Pa0001Controller::class, 'errormsg'])->name('pa0001.errormsg');
  Route::get('/count', [Pa0001Controller::class, 'count'])->name('pa0001.count');
  Route::get('/count/narrowdown', [Pa0001Controller::class, 'countNarrowDown'])->name('pa0001.count_narrowdown');
  Route::get('/count/top', [Pa0001Controller::class, 'countTop'])->name('pa0001.count_top');
  Route::get('/count/department', [Pa0001Controller::class, 'countSearchDepartment'])->name('pa0001.count_search_department');
  Route::get('/count/personnel', [Pa0001Controller::class, 'countSearchPersonnel'])->name('pa0001.count_search_personnel');
  Route::get('/clipboard/{id}', [Pa0001Controller::class, 'clipboard'])->name('pa0001.clipboard');
  Route::get('/deleteclipboard', [Pa0001Controller::class, 'deleteclipboard'])->name('pa0001.deleteclipboard');
  Route::get('/redirect', [Pa0001Controller::class, 'redirect'])->name('pa0001.redirect');
  Route::get('/bs/top', [Pa0001Controller::class, 'top'])->name('pa0001.top');
});

//Psbs01Controllerに関するルーティング
Route::prefix('psbs01')->group(function () {
  Route::post('/bscopy', [Psbs01Controller::class, 'copy'])->name('psbs01.copy');
  Route::get('/{id}/{id2}', [Psji01Controller::class, 'search']);
  Route::post('/{id}/{id2}', [Psbs01Controller::class, 'search'])->name('psbs01.search');
  Route::patch('/update', [Psbs01Controller::class, 'update'])->name('psbs01.update');
  Route::patch('/high/{id}', [Psbs01Controller::class, 'hierarchyUpdate'])->name('psbs01.hierarchyUpdate');
  Route::post('/delete/{id}/{id2}', [Psbs01Controller::class, 'delete'])->name('psbs01.delete');
});

//Psji01Controllerに関するルーティング
Route::prefix('psji01')->group(function () {
  Route::post('/jicopy', [Psji01Controller::class, 'copy'])->name('psji01.copy');
  Route::patch('/mail/send', [Psji01Controller::class, 'mailSend'])->name('psji01.send');
  Route::get('/{id}/{id2}', [Psji01Controller::class, 'search']);
  Route::post('/{id}/{id2}', [Psji01Controller::class, 'search'])->name('psji01.search');
  Route::post('/jidestroy/{id}/{id2}', [Psji01Controller::class, 'destroy'])->name('psji01.destroy');
});

//Ptcm01Controllerに関するルーティング
Route::post('ptcm01/ptcmdelete/{id}/{id2}', [Ptcm01Controller::class, 'delete'])->name('ptcm01.delete');


//PslgControllerに関するルーティング
Route::prefix('pslg')->group(function () {
  Route::get('/', [Pslg01Controller::class, 'index'])->name('pslg.index');
  Route::post('/', [Pslg01Controller::class, 'clear'])->name('pslg.clear');
  Route::get('/create', function(){ return view('pslg01.pslg01');});
  Route::post('/create', [Pslg01Controller::class, 'create'])->name('pslg01.create');
  Route::post('/download', [Pslg01Controller::class, 'download'])->name('pslg01.download');
});

//ネットワーク設定の画面のルート
//Psnw01Controllerに関するルーティング
Route::prefix('psnw01')->group(function () {
  Route::get('/', [Psnw01Controller::class, 'index'])->name('psnw01.index');
  //ネットワーククライアントのデータベース保存
  Route::post('/create', [Psnw01Controller::class, 'create'])->name('psnw01.create');
  //メール送信試験
  Route::post('/send', [Psnw01Controller::class, 'send'])->name('psnw01.send');
  //メール受信試験
  Route::post('/receive', [Psnw01Controller::class, 'receive'])->name('psnw01.receive');
});

//Pppu01Controllerに関するルーティング
Route::resource('pppu01', Pppu01Controller::class)->only('index');

//Pskb01Controllerに関するルーティング
Route::prefix('pskb')->group(function () {
  Route::get('/', [Pskb01Controller::class, 'index'])->name('pskb.index');
});