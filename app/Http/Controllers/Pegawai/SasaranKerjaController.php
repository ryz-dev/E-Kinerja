<?php

namespace App\Http\Controllers\Pegawai;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SkpPegawaiRepository;

class SasaranKerjaController extends Controller
{
    public function index(){
        return view('layouts.users.sasaran-kerja.index');
    }

    public function add(Request $r)
    {
        $sasaran_kerja= new SkpPegawaiRepository($r['uuid'], $r['periode']);
        $periode = $r['periode'];
        $sasaran_kerja = $sasaran_kerja->sasaranKerjaData();

        return view('layouts.users.sasaran-kerja.add', compact('sasaran_kerja', 'periode'));
    }

    public function sasaranKerja(Request $r){
        $sasaran_kerja = new SkpPegawaiRepository;

        $date = date('Y-m-d', strtotime($r['d']));
        return $this->ApiSpecResponses($sasaran_kerja->getIndexData($date));
    }

    public function store(Request $r){

        $sasaran_kerja = new SkpPegawaiRepository($r['nip'], $r['periode']);

        if ($sasaran_kerja->save($r)) {

            $r->session()->flash('message', 'SKP berhasil tersimpan');

            return redirect()->route('sasaran-kerja.index');
        }
    }

    public function edit(Request $r){
        $sasaran_kerja= new SkpPegawaiRepository($r['uuid'], $r['periode']);
        $periode = $r['periode'];
        $sasaran_kerja = $sasaran_kerja->sasaranKerjaData();

        return view('layouts.users.sasaran-kerja.edit', compact('sasaran_kerja', 'periode'));
    }

    public function update(Request $r){
        $sasaran_kerja= new SkpPegawaiRepository($r['nip'], $r['periode']);


        if ($sasaran_kerja->edit($r)) {

            $r->session()->flash('message', 'SKP berhasil di update');

            return redirect()->route('sasaran-kerja.index');
        }
    }

    public function destroy(Request $r){
        $sasaran_kerja= new SkpPegawaiRepository($r['uuid'], $r['periode']);
        return $this->ApiSpecResponses($sasaran_kerja->deleteSkp());
    }

}
