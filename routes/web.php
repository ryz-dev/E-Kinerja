<?php

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

/*
  Route Admin
*/

//Route::group(['prefix' => 'master-data','namespace' => 'MasterData', 'middleware' => 'can:master-data'],function (){
Route::group(['prefix' => 'master-data','namespace' => 'MasterData'],function (){
    Route::get('',function() {
      return view('layouts/admin/home/index');
    });
    Route::group(['prefix' => 'pegawai'],function (){
        Route::get('','PegawaiController@index')->name('pegawai.index');
        Route::get('add','PegawaiController@add')->name('pegawai.add');
        Route::get('{id}','PegawaiController@show')->name('pegawai.detail');
        Route::get('edit/{id}','PegawaiController@edit')->name('pegawai.edit');
        Route::post('','PegawaiController@store')->name('pegawai.store');
        Route::post('{id}','PegawaiController@update')->name('pegawai.update');
        Route::post('delete/{id}','PegawaiController@delete')->name('pegawai.delete');
    });
    Route::group(['prefix' => 'eselon'],function (){
        Route::get('','EselonController@index')->name('eselon.index');
        Route::get('add','EselonController@add')->name('eselon.add');
        Route::get('{id}','EselonController@show')->name('eselon.detail');
        Route::get('edit/{id}','EselonController@edit')->name('eselon.edit');
        Route::post('','EselonController@store')->name('eselon.store');
        Route::post('{id}','EselonController@update')->name('eselon.update');
        Route::post('delete/{id}','EselonController@delete')->name('eselon.delete');
    });
    Route::group(['prefix' => 'hari-kerja'],function (){
        Route::get('','HariKerjaController@index')->name('hari_kerja');
        Route::get('add','HariKerjaController@add')->name('hari_kerja_add');
        Route::post('','HariKerjaController@store')->name('hari_kerja_store');
        Route::get('edit/{id}','HariKerjaController@edit')->name('hari_kerja_edit');
        Route::post('{id}','HariKerjaController@update')->name('hari_kerja_update');
        /* Route::get('{id}','HariKerjaController@show'); */
    });
    Route::group(['prefix' => 'jabatan'],function (){
        Route::get('','JabatanController@index')->name('jabatan.index');
        Route::get('add','JabatanController@add')->name('jabatan.add');
        Route::get('{id}','JabatanController@show')->name('jabatan.detail');
        Route::get('edit/{id}','JabatanController@edit')->name('jabatan.edit');
        Route::post('','JabatanController@store')->name('jabatan.store');
        Route::post('{id}','JabatanController@update')->name('jabatan.update');
        Route::post('delete/{id}','JabatanController@delete')->name('jabatan.delete');
    });
    Route::group(['prefix' => 'agama'],function (){
        Route::get('','StaticDataController@getAgama');
        Route::get('{id}','StaticDataController@showAgama');
        Route::post('','StaticDataController@storeAgama');
        Route::post('{id}','StaticDataController@updateAgama');
        Route::post('delete/{id}','StaticDataController@deleteAgama');
    });
    Route::group(['prefix' => 'bulan'],function (){
        Route::get('','StaticDataController@getBulan');
        Route::get('{id}','StaticDataController@showBulan');
        Route::post('','StaticDataController@storeBulan');
        Route::post('{id}','StaticDataController@updateBulan');
        Route::post('delete/{id}','StaticDataController@deleteBulan');
    });
    Route::group(['prefix' => 'hari'],function (){
        Route::get('','StaticDataController@getHari');
        Route::get('{id}','StaticDataController@showHari');
        Route::post('','StaticDataController@storeHari');
        Route::post('{id}','StaticDataController@updateHari');
        Route::post('delete/{id}','StaticDataController@deleteHari');
    });
    Route::group(['prefix' => 'role-pegawai'], function(){
        Route::get('','RolePegawaiController@index');
    });
});
Route::group(['prefix' => 'api-web','namespace' => 'API'],function (){
    Route::group(['prefix' => 'pegawai'],function (){
        Route::get('','PegawaiController@listPegawai')->name('api.web.pegawai');
        Route::get('get-pagination','PegawaiController@getpage')->name('api.web.pegawai.page');
        Route::post('store','PegawaiController@storePegawai')->name('api.web.pegawai.store');
        Route::post('{id}','PegawaiController@updatePegawai')->name('api.web.pegawai.update');
        Route::post('delete/{id}','PegawaiController@deletePegawai')->name('api.web.pegawai.delete');
    });
    Route::group(['prefix' => 'hari-kerja'],function (){
//        Route::get('','HariKerjaController@index')->name('list_hari_kerja');
//        Route::get('get-pagination','HariKerjaController@getpage')->name('page_hari_kerja');
//        Route::post('delete','HariKerjaController@delete')->name('delete_hari_kerja');
    });
    Route::group(['prefix' => 'jabatan'],function (){
        Route::get('','JabatanController@listJabatan')->name('api.web.jabatan');
        Route::get('get-pagination','JabatanController@getpage')->name('api.web.jabatan.page');
        Route::post('store','JabatanController@storeJabatan')->name('api.web.jabatan.store');
        Route::post('{id}','JabatanController@updateJabatan')->name('api.web.jabatan.update');
        Route::post('delete/{id}','JabatanController@deleteJabatan')->name('api.web.jabatan.delete');
    });
    Route::group(['prefix' => 'eselon'],function (){
        Route::get('','EselonController@listEselon')->name('api.web.eselon');
        Route::get('get-pagination','EselonController@getpage')->name('api.web.eselon.page');
        Route::post('store','EselonController@storeEselon')->name('api.web.eselon.store');
        Route::post('{id}','EselonController@updateEselon')->name('api.web.eselon.update');
        Route::post('delete/{id}','EselonController@deleteEselon')->name('api.web.eselon.delete');
    });
    Route::group(['prefix'=> 'role-pegawai'], function(){
        Route::get('', 'RolePegawaiController@listRole')->name('api.web.list.role');
        Route::get('get-paginate', 'RolePegawaiController@getPage')->name('api.web.page.role.pegawai');
      });
    
});

/*
  Route User
*/


/* Absen Routing */
Route::group(['prefix' => 'absensi','namespace' => 'Absen'],function (){
  Route::resource('checkinout','CheckinoutController');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/rekap-bulanan','RekapBulananController@rekapBulanan')->name('rekap-bulanan.index');


// Route::get('/test', function(){
//     dd(\App\Models\MasterData\Role::find(1)->permissions);
// });
//
// Route::get('/', function () {
//     return view('welcome');
// });
