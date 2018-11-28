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

Route::get('/', function(){
    return redirect('/login');
});

Route::group(['prefix'=>'admin', 'namespace'=>'Auth'], function(){
    Route::get('', function(){
        return redirect()->route('admin-login-index');
    });
    Route::get('/login', 'AdminLoginController@showLoginForm')->name('admin-login-index');
    Route::post('', 'AdminLoginController@login')->name('admin-login');
});
Route::group(['prefix' => 'master-data','namespace' => 'MasterData', 'middleware' => 'can:master-data' ],function (){
    Route::get('',function() {
      return redirect()->route('pegawai.index');
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
        Route::get('','RolePegawaiController@index')->name('role-pegawai.index');
    });
    Route::group(['prefix' => 'skpd'],function (){
        Route::get('','SkpdController@index')->name('skpd.index');
        Route::get('add','SkpdController@add')->name('skpd.add');
        Route::get('{id}','SkpdController@show')->name('skpd.detail');
        Route::get('edit/{id}','SkpdController@edit')->name('skpd.edit');
        Route::post('','SkpdController@store')->name('skpd.store');
        Route::post('{id}','SkpdController@update')->name('skpd.update');
        Route::post('delete/{id}','SkpdController@delete')->name('skpd.delete');
    });
});

//API-WEB
Route::group(['prefix' => 'api-web','namespace' => 'API'],function (){
    Route::group(['prefix'=> 'monitoring-absen'], function(){
        Route::get('','MonitoringAbsenController@dataAbsensi')->name('api.web.monitoring.absen');
        Route::get('getpage','MonitoringAbsenController@getPage')->name('api.web.monitoring.absen.page');
    });
    Route::group(['prefix' => 'master-data'],function (){
        Route::group(['prefix' => 'pegawai'],function (){
            Route::get('','PegawaiController@listPegawai')->name('api.web.master-data.pegawai');
            Route::get('get-pagination','PegawaiController@getpage')->name('api.web.master-data.pegawai.page');
            Route::post('store','PegawaiController@storePegawai')->name('api.web.master-data.pegawai.store');
            Route::post('{id}','PegawaiController@updatePegawai')->name('api.web.master-data.pegawai.update');
            Route::post('delete/{id}','PegawaiController@deletePegawai')->name('api.web.master-data.pegawai.delete');
        });
        Route::group(['prefix' => 'hari-kerja'],function (){
//        Route::get('','HariKerjaController@index')->name('list_hari_kerja');
//        Route::get('get-pagination','HariKerjaController@getpage')->name('page_hari_kerja');
//        Route::post('delete','HariKerjaController@delete')->name('delete_hari_kerja');
        });
        Route::group(['prefix' => 'jabatan'],function (){
            Route::get('','JabatanController@listJabatan')->name('api.web.master-data.jabatan');
            Route::get('get-pagination','JabatanController@getpage')->name('api.web.master-data.jabatan.page');
            Route::post('store','JabatanController@storeJabatan')->name('api.web.master-data.jabatan.store');
            Route::post('{id}','JabatanController@updateJabatan')->name('api.web.master-data.jabatan.update');
            Route::post('delete/{id}','JabatanController@deleteJabatan')->name('api.web.master-data.jabatan.delete');
        });
        Route::group(['prefix' => 'eselon'],function (){
            Route::get('','EselonController@listEselon')->name('api.web.master-data.eselon');
            Route::get('get-pagination','EselonController@getpage')->name('api.web.master-data.eselon.page');
            Route::post('store','EselonController@storeEselon')->name('api.web.master-data.eselon.store');
            Route::post('{id}','EselonController@updateEselon')->name('api.web.master-data.eselon.update');
            Route::post('delete/{id}','EselonController@deleteEselon')->name('api.web.master-data.eselon.delete');
        });
        Route::group(['prefix' => 'skpd'],function (){
            Route::get('','SkpdController@listSkpd')->name('api.web.master-data.skpd');
            Route::get('get-pagination','SkpdController@getpage')->name('api.web.master-data.skpd.page');
            Route::post('store','SkpdController@storeSkpd')->name('api.web.master-data.skpd.store');
            Route::post('{id}','SkpdController@updateSkpd')->name('api.web.master-data.skpd.update');
            Route::post('delete/{id}','SkpdController@deleteSkpd')->name('api.web.master-data.skpd.delete');
        });
        Route::group(['prefix'=> 'role-pegawai'], function(){
            Route::get('', 'RolePegawaiController@listRole')->name('api.web.master-data.list.role');
            Route::get('get-paginate', 'RolePegawaiController@getPage')->name('api.web.master-data.page.role.pegawai');
            Route::get('get-roles', 'RolePegawaiController@getRoles')->name('api.web.master-data.role.get');
            Route::post('store', 'RolePegawaiController@store')->name('api.web.master-data.role.store');
            Route::post('delete', 'RolePegawaiController@delete')->name('api.web.master-data.role.delete');
        });
    });
    Route::group(['prefix' => 'rekap-bulanan'],function (){
        Route::get('/get-bawahan','RekapBulananController@getBawahan')->name('api.web.rekap-bulanan.get-bawahan');
        Route::get('/{nip}/{tanggal}','RekapBulananController@getDetailRekap')->name('api.web.rekap-detail');
        Route::get('/{nip}/{bulan?}/{tahun?}','RekapBulananController@getRekap')->name('api.web.rekap-bulanan');
    });
    Route::group(['prefix' => 'penilaian-kinerja'],function (){
        Route::get('/get-bawahan','PenilaianKinerjaController@getBawahan')->name('api.web.get-bawahan-kinerja');
        Route::get('/{nip}','PenilaianKinerjaController@getKinerja')->name('api.web.get-penilaian-kinerja');
        Route::post('reply','PenilaianKinerjaController@replyKinerja')->name('api.web.reply-penilaian-kinerja');
    });
    Route::group(['prefix' => 'penilaian-etika'], function(){
        Route::get('/get-pegawai', 'PenilaianEtikaController@getPegawai')->name('api.web.penilaian-etika.get-pegawai');
        Route::post('', 'PenilaianEtikaController@storePenilaian')->name('api.web.penilaian-etika.store.penilaian');
    });
    Route::group(['prefix' => 'kinerja'],function (){
        Route::post('','KinerjaController@inputKinerja')->name('api.web.input-kinerja.post');
        Route::get('{bulan?}/{tahun?}','KinerjaController@tunjanganKinerja')->name('api.web.tunjangan-kinerja.get');
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
Route::get('/',function (){
    return redirect('login');
});
Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => 'auth'],function (){
    Route::get('/monitoring-absen', 'MonitoringAbsenController@index')->name('monitoring.absen.index')->middleware('can:monitoring-absen');
    Route::get('/rekap-bulanan','RekapBulananController@rekapBulanan')->name('rekap-bulanan.index')->middleware('can:rekap-bulanan');
    Route::get('/input-kinerja','InputKinerjaController@inputKinerja')->name('input-kinerja.index');
    Route::get('/penilaian-kinerja','PenilainKinerjaController@penilaianKinerja')->name('penilaian-kinerja.index')->middleware('can:penilaian-kinerja');
    Route::get('/penilaian-etika', 'PenilaianEtikaController@index')->name('penilaian-etika.index')->middleware('can:penilaian-etika');
    Route::get('/tunjangan-kinerja','TunjanganKinerjaController@index')->name('tunjangan-kinerja.index')->middleware('can:tunjangan-kinerja');
});
// Route::get('/test', function(){
//     dd(\App\Models\MasterData\Role::find(1)->permissions);
// });
//
// Route::get('/', function () {
//     return view('welcome');
// });
