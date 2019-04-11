<?php
Route::get('/', function(){
    return redirect('/login');
});
Route::group(['middleware' => 'auth'],function (){
    Route::group(['namespace' => 'Pegawai'],function (){
        Route::group(['prefix' => 'monitoring-absen','middleware' => 'can:monitoring-absen'],function (){
            Route::group(['prefix' => 'api'],function (){
                Route::get('','MonitoringAbsenController@dataAbsensi')->name('monitoring-absen.api');
                Route::get('page','MonitoringAbsenController@getPage')->name('monitoring-absen.api.page');
            });
            Route::get('', 'MonitoringAbsenController@index')->name('monitoring.absen.index');
        });
        Route::group(['prefix' => 'rekap-bulanan','middleware' => 'can:rekap-bulanan'],function (){
            Route::group(['prefix' => 'api'],function (){
                Route::get('/get-bawahan','RekapBulananController@getBawahan')->name('rekap-bulanan.api.bawahan');
                Route::get('/{nip}/{tanggal}','RekapBulananController@getDetailRekap')->name('rekap-bulanan.api.detail');
                Route::get('/{nip}/{bulan?}/{tahun?}','RekapBulananController@getRekap')->name('rekap-bulanan.api.rekap');
            });
            Route::get('','RekapBulananController@rekapBulanan')->name('rekap-bulanan.index');
            Route::post('', 'RekapBulananController@downloadRekapBulanan')->name('download.rekap.bulanan');
        });
        Route::group(['prefix' => 'input-kinerja'],function (){
            Route::group(['prefix' => 'api'],function (){
                Route::post('','InputKinerjaController@inputKinerja')->name('input-kinerja.api.post');
                Route::get('draft','InputKinerjaController@getKinerjaTersimpan')->name('input-kinerja.api.draft');
                Route::post('delete/draft/{id}','InputKinerjaController@hapusKinerjaTersimpan')->name('input-kinerja.api.delete-draft');
            });
            Route::get('','InputKinerjaController@index')->name('input-kinerja.index');
        });
        Route::group(['prefix' => 'penilaian-kinerja','middleware' => 'can:penilaian-kinerja'],function (){
            Route::group(['prefix' => 'api'],function (){
                Route::get('/get-bawahan','PenilaianKinerjaController@getBawahan')->name('penilaian-kinerja.api.bawahan');
                Route::get('/{nip}','PenilaianKinerjaController@getKinerja')->name('penilaian-kinerja.api.kinerja');
                Route::post('reply','PenilaianKinerjaController@replyKinerja')->name('penilaian-kinerja.api.reply');
            });
            Route::get('','PenilainKinerjaController@penilaianKinerja')->name('penilaian-kinerja.index');
        });
        Route::group(['prefix' => 'tunjangan-kinerja','middleware' => 'can:tunjangan-kinerja'],function(){
            Route::group(['prefix' => 'api'],function (){
                Route::get('{bulan?}/{tahun?}','TunjanganKinerjaController@tunjanganKinerja')->name('tunjangan-kinerja.api');
            });
            Route::get('','TunjanganKinerjaController@index')->name('tunjangan-kinerja.index');
        });
        Route::group(['prefix' => 'sasaran-kerja'], function(){
            Route::group(['prefix'=> 'api'], function(){
                Route::get('', 'SasaranKerjaController@sasaranKerja')->name('sasaran-kerja.api.index');
            });

            Route::get('', 'SasaranKerjaController@index')->name('sasaran-kerja.index');
            Route::get('/add/', 'SasaranKerjaController@add')->name('sasaran-kerja.add');
            Route::post('/store/', 'SasaranKerjaController@store')->name('sasaran-kerja.store');
        });
    });
    Route::post('update-password','Admin/PegawaiController@updatePassword')->name('update-password');
});
Route::group(['prefix'=>'admin'], function(){
    Route::get('',function() {
        return redirect()->route('admin-login.index');
    });
    Route::group(['namespace' => 'Auth'],function (){
        Route::get('', function(){
            return redirect()->route('admin-login-index');
        });
        Route::get('/login', 'AdminLoginController@showLoginForm')->name('admin-login-index');
        Route::post('login', 'AdminLoginController@login')->name('admin-login');
    });
    Route::group(['middleware' => 'Auth'],function (){
        Route::group(['middleware' => 'can:master-data','namespace' => 'Admin'],function (){
            Route::group(['prefix' => 'pegawai'],function (){
                Route::group(['prefix' => 'api'],function (){
                    Route::get('','PegawaiController@list')->name('pegawai.api.index');
                    Route::get('download','PegawaiController@downloadRekapBulanan')->name('pegawai.api.download');
                    Route::get('page','PegawaiController@getPage')->name('pegawai.api.page');
                    Route::get('skpd','PegawaiController@getSkpd')->name('pegawai.api.skpd');
                    Route::get('{id}','PegawaiController@detail')->name('pegawai.api.detail');
                    Route::post('update-password','PegawaiController@updatePassword')->name('pegawai.api.update-password');
                    Route::post('','PegawaiController@store')->name('pegawai.api.store');
                    Route::post('{id}','PegawaiController@update')->name('pegawai.api.update');
                    Route::post('delete/{id}','PegawaiController@delete')->name('pegawai.api.delete');
                    Route::post('restore/{nip}','PegawaiController@restorePegawai')->name('pegawai.api.restore');
                });
                Route::post('import','PegawaiController@import')->name('pegawai.import');
                Route::get('','PegawaiController@index')->name('pegawai.index');
                Route::get('add','PegawaiController@add')->name('pegawai.add');
                Route::get('deleted','PegawaiController@deleted')->name('pegawai.deleted');
                Route::get('{id}','PegawaiController@show')->name('pegawai.detail');
                Route::get('edit/{id}','PegawaiController@edit')->name('pegawai.edit');
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
            Route::group(['prefix' => 'golongan'],function (){
                Route::group(['prefix' => 'api'],function (){
                    Route::get('','GolonganController@list')->name('golongan.api.index');
                    Route::get('page','GolonganController@getPage')->name('golongan.api.page');
                    Route::get('{id}','GolonganController@detail')->name('golongan.api.detail');
                    Route::post('','GolonganController@store')->name('golongan.api.store');
                    Route::post('{id}','GolonganController@update')->name('golongan.api.update');
                    Route::post('delete/{id}','GolonganController@delete')->name('golongan.api.delete');
                });
                Route::get('','GolonganController@index')->name('golongan.index');
                Route::get('add','GolonganController@add')->name('golongan.add');
                Route::get('{id}','GolonganController@show')->name('golongan.detail');
                Route::get('edit/{id}','GolonganController@edit')->name('golongan.edit');
            });
            Route::group(['prefix' => 'hari-kerja'],function (){
                Route::group(['prefix' => 'api'],function (){
                    Route::get('','HariKerjaController@list')->name('hari-kerja.api.index');
                    Route::get('page','HariKerjaController@getPage')->name('hari-kerja.api.page');
                    Route::get('{id}','HariKerjaController@detail')->name('hari-kerja.api.detail');
                    Route::post('','HariKerjaController@store')->name('hari-kerja.api.store');
                    Route::post('{id}','HariKerjaController@update')->name('hari-kerja.api.update');
                    Route::post('delete/{id}','HariKerjaController@delete')->name('hari-kerja.api.delete');
                });
                Route::get('','HariKerjaController@index')->name('hari_kerja');
                Route::get('add','HariKerjaController@add')->name('hari_kerja_add');
                Route::post('','HariKerjaController@store')->name('hari_kerja_store');
                Route::get('edit/{id}','HariKerjaController@edit')->name('hari_kerja_edit');
                Route::post('{id}','HariKerjaController@update')->name('hari_kerja_update');
                /* Route::get('{id}','HariKerjaController@show'); */
            });
            Route::group(['prefix' => 'jabatan'],function (){
                Route::group(['prefix' => 'api'],function (){
                    Route::get('','JabatanController@list')->name('jabatan.api.index');
                    Route::get('page','JabatanController@getPage')->name('jabatan.api.page');
                    Route::get('{id}','JabatanController@detail')->name('jabatan.api.detail');
                    Route::post('','JabatanController@store')->name('jabatan.api.store');
                    Route::post('{id}','JabatanController@update')->name('jabatan.api.update');
                    Route::post('delete/{id}','JabatanController@delete')->name('jabatan.api.delete');
                });
                Route::get('','JabatanController@index')->name('jabatan.index');
                Route::get('add','JabatanController@add')->name('jabatan.add');
                Route::get('{id}','JabatanController@show')->name('jabatan.detail');
                Route::get('edit/{id}','JabatanController@edit')->name('jabatan.edit');
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
                Route::group(['prefix' => 'api'],function (){
                    Route::get('', 'RolePegawaiController@list')->name('role-pegawai.api.index');
                    Route::get('page', 'RolePegawaiController@getPage')->name('role-pegawai.api.page');
                    Route::get('roles', 'RolePegawaiController@getRoles')->name('role-pegawai.api.roles');
                    Route::post('store', 'RolePegawaiController@store')->name('role-pegawai.api.store');
                    Route::post('delete', 'RolePegawaiController@delete')->name('role-pegawai.api.delete');
                });
                Route::get('','RolePegawaiController@index')->name('role-pegawai.index');
            });
            Route::group(['prefix' => 'skpd'],function (){
                Route::group(['prefix' => 'api'],function (){
                    Route::get('','SkpdController@list')->name('skpd.api.index');
                    Route::get('page','SkpdController@getPage')->name('skpd.api.page');
                    Route::get('{id}','SkpdController@detail')->name('skpd.api.detail');
                    Route::post('','SkpdController@store')->name('skpd.api.store');
                    Route::post('{id}','SkpdController@update')->name('skpd.api.update');
                    Route::post('delete/{id}','SkpdController@delete')->name('skpd.api.delete');
                });
                Route::get('','SkpdController@index')->name('skpd.index');
                Route::get('add','SkpdController@add')->name('skpd.add');
                Route::get('{id}','SkpdController@show')->name('skpd.detail');
                Route::get('edit/{id}','SkpdController@edit')->name('skpd.edit');
            });
            Route::group(['prefix' => 'checkinout'], function(){
                Route::get('','CheckinoutController@index')->name('checkinout.index');
                Route::get('add','CheckinoutController@create')->name('checkinout.create');
                Route::get('{id}','CheckinoutController@show')->name('checkinout.show');
                Route::get('edit/{id}','CheckinoutController@edit')->name('checkinout.edit');
                Route::post('','CheckinoutController@store')->name('checkinout.store');
                Route::post('{id}','CheckinoutController@update')->name('checkinout.update');
                Route::post('delete/{id}','CheckinoutController@destroy')->name('api.checkinout.delete-absen');
                Route::get('get/peg-cehckinout','CheckinoutController@getPegawai')->name('api.checkinout.peg-absen');
            });

            Route::group(['prefix'=> 'mesin-absen-upacara'], function(){
                Route::group(['prefix' => 'api'],function (){
                    Route::get('','AbsenUpacaraController@list')->name('absen-upacara.api.index');
                    Route::get('/page','AbsenUpacaraController@page')->name('absen-upacara.api.page');
                    Route::post('/store','AbsenUpacaraController@store')->name('absen-upacara.api.store');
                    Route::post('/delete','AbsenUpacaraController@delete')->name('absen-upacara.api.delete');
                    Route::post('','AbsenUpacaraController@update')->name('absen-upacara.api.update');
                });
                Route::get('', 'AbsenUpacaraController@index')->name('absen-upacara.index');
            });
        });
    });
});
//API-WEB
Route::group(['prefix' => 'api-web','namespace' => 'API'],function (){
    Route::group(['prefix' => 'skp'],function (){
        Route::get('','SkpController@listSkpTask')->name('api.web.skp.list');
        Route::get('{id}','SkpController@detailSkpTask')->name('api.web.skp.detail');
        Route::post('','SkpController@storeSkpTask')->name('api.web.skp.store');
        Route::post('{id}','SkpController@updateSkp')->name('api.web.skp.update');
        Route::delete('{id}','SkpController@deleteSkp')->name('api.web.skp.delete');
        Route::get('getpage','SkpController@getPageSkp')->name('api.web.skp.page');
    });
    Route::group(['prefix' => 'skp-pegawai'],function (){
        Route::get('','SkpPegawaiController@listSkpPegawai')->name('api.web.skp-pegawai.list');
        Route::get('{id}','SkpPegawaiController@detailSkpPegawai')->name('api.web.skp-pegawai.detail');
        Route::post('','SkpPegawaiController@storeSkpPegawai')->name('api.web.skp-pegawai.store');
        Route::post('{id}','SkpPegawaiController@updateSkpPegawai')->name('api.web.skp-pegawai.update');
        Route::delete('{id}','SkpPegawaiController@deleteSkpPegawai')->name('api.web.skp-pegawai.delete');
        Route::get('getpage','SkpPegawaiController@getPageSkpPegawai')->name('api.web.skp-pegawai.page');
    });
    /* Absen Routing */
    Route::group(['prefix' => 'absensi'],function (){
      Route::get('test', 'CheckinoutController@list')->name('api.web.master-data.absen-list');
      Route::get('get-pagination', 'CheckinoutController@getPage')->name('api.web.master-data.checkinout.page');
    });

});

/*
  Route User
*/

Auth::routes();
