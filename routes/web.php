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
Route::get('/test', function(){
    dd(\App\Models\MasterData\Role::find(1)->permissions);
});

Route::get('/', function () {
    return view('welcome');
});

//Route::group(['prefix' => 'master-data','namespace' => 'MasterData', 'middleware' => 'can:master-data'],function (){
Route::group(['prefix' => 'master-data','namespace' => 'MasterData'],function (){
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
        Route::get('','EselonController@index');
        Route::get('{id}','EselonController@show');
        Route::post('','EselonController@store');
        Route::post('{id}','EselonController@update');
        Route::post('delete/{id}','EselonController@delete');
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
});

// absen routing
Route::group(['prefix' => 'absensi','namespace' => 'Absen'],function (){
        Route::resource('checkinout','CheckinoutController');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
