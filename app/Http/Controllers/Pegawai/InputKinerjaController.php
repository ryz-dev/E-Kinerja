<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\SkpPegawai;
use App\Repositories\KinerjaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputKinerjaController extends Controller
{
    protected $kinerja;

    public function __construct(KinerjaRepository $kinerja)
    {
        $this->kinerja = $kinerja;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role()->first()->id;
        $permission = $user->role()->first()->permissions;
        $skp_pegawai = SkpPegawai::with('skpTask')->whereMonth('periode',date('m'))->where('nip_pegawai',$user->nip)->get();
        if ($role == 2 || $role == 3) {
            if ($permission['input-kinerja'] == false)
                return redirect()->route('monitoring.absen.index');
        }
        return view('layouts.users.input-kinerja.index',compact('skp_pegawai'));
    }

    public function getKinerjaTersimpan()
    {
        $nip = auth('web')->user()->nip;
        if ($kinerja_tersimpan = $this->kinerja->getKinerjaTersimpan($nip)) {
            return $this->ApiSpecResponses($kinerja_tersimpan);
        }
        return null;
    }

    public function hapusKinerjaTersimpan($id)
    {
        $nip = auth('web')->user()->nip;
        if ($this->kinerja->deleteKinerjaTersimpan($id, $nip)) {
            return $this->ApiSpecResponses([
                'message' => 'Berhasil menghapus draft'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagagl menghapus draft'
        ]);
    }

    public function inputKinerja(Request $request)
    {
        $nip = auth('web')->user()->nip;
        return $this->ApiSpecResponses($this->kinerja->inputKinerja($request->input(), $nip));
    }

}
