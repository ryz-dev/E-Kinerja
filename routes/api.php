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

/*Route::middleware('auth:api')->get('/v1', function (Request $request) {
     return $request->user()->load('role');
 });*/
Route::group(['preifx' => 'v2'],function (){
    Route::post('/login', 'Api\LoginPassportController@getLogin');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/status', 'Api\LoginPassportController@getStatus');
        Route::get('/refresh', 'Api\LoginPassportController@getRefresh');
        Route::get('/logout', 'Api\LoginPassportController@getLogout');
        Route::post('/change-password', 'Api\LoginPassportController@getChangePassword');
    });
    Route::group(['middleware' => 'auth:api', 'namespace' => 'APIMobile'], function () {
        Route::group(['prefix' => 'monitoring-absen', 'middleware' => 'can:monitoring-absen'], function () {
            Route::get('', 'MonitoringAbsenController@dataAbsensi')->name('api.mobile.monitoring.absen');
        });
        Route::group(['prefix' => 'rekap-bulanan', 'middleware' => 'can:rekap-bulanan'], function () {
            Route::get('/get-bawahan', 'RekapBulananController@getBawahan')->name('api.mobile.rekap-bulanan.get-bawahan');
            Route::get('/{nip}/{tanggal}', 'RekapBulananController@getDetailRekap')->name('api.mobile.rekap-detail');
            Route::get('/{nip}/{bulan?}/{tahun?}', 'RekapBulananController@getRekap')->name('api.mobile.rekap-bulanan');
        });
        Route::group(['prefix' => 'penilaian-kinerja', 'middleware' => 'can:penilaian-kinerja'], function () {
            Route::get('/get-bawahan', 'PenilaianKinerjaController@getBawahan')->name('api.mobile.get-bawahan-kinerja');
            Route::get('/{nip}', 'PenilaianKinerjaController@getKinerja')->name('api.mobile.get-penilaian-kinerja');
            Route::post('reply', 'PenilaianKinerjaController@replyKinerja')->name('api.mobile.reply-penilaian-kinerja');
        });
        Route::group(['prefix' => 'kinerja','middleware' => 'can:tunjangan-kinerja'], function () {
            Route::post('reply', 'KinerjaController@inputKinerja')->name('api.mobile.input-kinerja.post');
            Route::get('cek', 'KinerjaController@cekKinerja')->name('api.mobile.cek-kinerja.get');
            Route::get('skp', 'KinerjaController@listSkp')->name('api.mobile.skp-kinerja.get');
            Route::get('draft','KinerjaController@getKinerjaTersimpan')->name('api.web.input-kinerja.draft');
            Route::post('delete/draft/{id}','KinerjaController@hapusKinerjaTersimpan')->name('api.web.input-kinerja.delete-draft');
            Route::get('/{tgl?}', 'KinerjaController@detailKinerja')->name('api.mobile.detail-kinerja.get');
            Route::get('/{bulan?}/{tahun?}', 'KinerjaController@tunjanganKinerja')->name('api.mobile.tunjangan-kinerja.get');
        });
        Route::group(['prefix' => 'master-data'], function () {
            Route::group(['prefix' => 'skpd'], function () {
                Route::get('', 'SkpdController@listSkpd')->name('api.mobile.master-data.skpd');
            });
        });
    });
    Route::group(['namespace' => 'API'],function (){
        Route::group(['prefix' => 'pegawai'], function () {
            Route::get('', 'PegawaiController@listPegawai')->name('list_pegawai');
            Route::get('get-pagination', 'PegawaiController@getpage')->name('page_pegawai');
            // Route::get('{id}','PegawaiController@show');
            // Route::post('','PegawaiController@store');
            // Route::post('{id}','PegawaiController@update');
            // Route::post('delete/{id}','PegawaiController@delete');
        });
        Route::group(['prefix' => 'hari-kerja'], function () {
            Route::get('', 'HariKerjaController@index')->name('list_hari_kerja');
            Route::get('get-pagination', 'HariKerjaController@getpage')->name('page_hari_kerja');
            // Route::get('{id}','HariKerjaController@show');
            // Route::post('','HariKerjaController@store');
            // Route::post('{id}','HariKerjaController@update');
            Route::post('delete', 'HariKerjaController@delete')->name('delete_hari_kerja');
        });
        Route::group(['prefix' => 'jabatan'], function () {
            Route::get('', 'JabatanController@listJabatan')->name('list_jabatan');
            Route::get('get-pagination', 'JabatanController@getpage')->name('page_jabatan');
        });

        Route::group(['prefix' => 'eselon'], function () {
            Route::get('', 'EselonController@listEselon')->name('list_eselon');
            Route::get('get-pagination', 'EselonController@getpage')->name('page_eselon');
        });

        Route::group(['prefix' => 'checklock'], function () {
            Route::post('', 'ReceivedController@receiver')->name('api_checklock');
        });
    });
});
Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', 'Api\LoginPassportController@getLogin');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/status', 'Api\LoginPassportController@getStatus');
        Route::get('/refresh', 'Api\LoginPassportController@getRefresh');
        Route::get('/logout', 'Api\LoginPassportController@getLogout');
        Route::post('/change-password', 'Api\LoginPassportController@getChangePassword');
    });
    Route::group(['middleware' => 'auth:api', 'namespace' => 'APIMobile'], function () {
        Route::group(['prefix' => 'monitoring-absen', 'middleware' => 'can:monitoring-absen'], function () {
            Route::get('', 'MonitoringAbsenController@dataAbsensi')->name('api.mobile.monitoring.absen');
        });
        Route::group(['prefix' => 'rekap-bulanan', 'middleware' => 'can:rekap-bulanan'], function () {
            Route::get('/get-bawahan', 'RekapBulananController@getBawahan')->name('api.mobile.rekap-bulanan.get-bawahan');
            Route::get('/{nip}/{tanggal}', 'RekapBulananController@getDetailRekap')->name('api.mobile.rekap-detail');
            Route::get('/{nip}/{bulan?}/{tahun?}', 'RekapBulananController@getRekap')->name('api.mobile.rekap-bulanan');
        });
        Route::group(['prefix' => 'penilaian-kinerja', 'middleware' => 'can:penilaian-kinerja'], function () {
            Route::get('/get-bawahan', 'PenilaianKinerjaController@getBawahan')->name('api.mobile.get-bawahan-kinerja');
            Route::get('/{nip}', 'PenilaianKinerjaController@getKinerja')->name('api.mobile.get-penilaian-kinerja');
            Route::post('reply', 'PenilaianKinerjaController@replyKinerja')->name('api.mobile.reply-penilaian-kinerja');
        });
        Route::group(['prefix' => 'kinerja','middleware' => 'can:tunjangan-kinerja'], function () {
            Route::post('reply', 'KinerjaController@inputKinerja')->name('api.mobile.input-kinerja.post');
            Route::get('cek', 'KinerjaController@cekKinerja')->name('api.mobile.cek-kinerja.get');
            Route::get('skp', 'KinerjaController@listSkp')->name('api.mobile.skp-kinerja.get');
            Route::get('draft','KinerjaController@getKinerjaTersimpan')->name('api.web.input-kinerja.draft');
            Route::post('delete/draft/{id}','KinerjaController@hapusKinerjaTersimpan')->name('api.web.input-kinerja.delete-draft');
            Route::get('/{tgl?}', 'KinerjaController@detailKinerja')->name('api.mobile.detail-kinerja.get');
            Route::get('/{bulan?}/{tahun?}', 'KinerjaController@tunjanganKinerja')->name('api.mobile.tunjangan-kinerja.get');
        });
        Route::group(['prefix' => 'master-data'], function () {
            Route::group(['prefix' => 'skpd'], function () {
                Route::get('', 'SkpdController@listSkpd')->name('api.mobile.master-data.skpd');
            });
        });
    });
});

Route::group(['prefix' => '/v1', 'namespace' => 'API'], function () {
    Route::group(['prefix' => 'pegawai'], function () {
        Route::get('', 'PegawaiController@listPegawai')->name('list_pegawai');
        Route::get('get-pagination', 'PegawaiController@getpage')->name('page_pegawai');
        // Route::get('{id}','PegawaiController@show');
        // Route::post('','PegawaiController@store');
        // Route::post('{id}','PegawaiController@update');
        // Route::post('delete/{id}','PegawaiController@delete');
    });
    Route::group(['prefix' => 'hari-kerja'], function () {
        Route::get('', 'HariKerjaController@index')->name('list_hari_kerja');
        Route::get('get-pagination', 'HariKerjaController@getpage')->name('page_hari_kerja');
        // Route::get('{id}','HariKerjaController@show');
        // Route::post('','HariKerjaController@store');
        // Route::post('{id}','HariKerjaController@update');
        Route::post('delete', 'HariKerjaController@delete')->name('delete_hari_kerja');
    });
    Route::group(['prefix' => 'jabatan'], function () {
        Route::get('', 'JabatanController@listJabatan')->name('list_jabatan');
        Route::get('get-pagination', 'JabatanController@getpage')->name('page_jabatan');
    });

    Route::group(['prefix' => 'eselon'], function () {
        Route::get('', 'EselonController@listEselon')->name('list_eselon');
        Route::get('get-pagination', 'EselonController@getpage')->name('page_eselon');
    });

    Route::group(['prefix' => 'checklock'], function () {
        Route::post('', 'ReceivedController@receiver')->name('api_checklock');
    });

});
