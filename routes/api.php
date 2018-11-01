<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/v1', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => '/v1','namespace' => 'API'],function (){
  Route::group(['prefix' => 'pegawai'],function (){
    Route::get('','PegawaiController@listPegawai')->name('list_pegawai');
    Route::get('get-pagination','PegawaiController@getpage')->name('page_pegawai');
    // Route::get('{id}','PegawaiController@show');
    // Route::post('','PegawaiController@store');
    // Route::post('{id}','PegawaiController@update');
    // Route::post('delete/{id}','PegawaiController@delete');
  });
  Route::group(['prefix' => 'jabatan'],function (){
      Route::get('','JabatanController@listJabatan')->name('list_jabatan');
      Route::get('get-pagination','JabatanController@getpage')->name('page_jabatan');
  });
});
