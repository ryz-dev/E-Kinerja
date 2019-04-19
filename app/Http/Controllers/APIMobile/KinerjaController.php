<?php


namespace App\Http\Controllers\APIMobile;

use App\Models\Absen\Kinerja;
use App\Models\SkpPegawai;
use App\Repositories\KinerjaRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KinerjaController extends ApiController
{
    protected $kinerja;

    public function __construct(KinerjaRepository $kinerja)
    {
        $this->kinerja = $kinerja;
    }

    public function listSkp()
    {
        $nip = Auth::user()->nip;
        $skp_pegawai = SkpPegawai::select('id', 'periode', 'id_skp','status')->with(['skpTask' => function ($query) {
            $query->select('id', 'task');
        }])->where('nip_pegawai', $nip)->whereMonth('periode', month(date('Y-m-d')))->whereYear('periode', year(date('Y-m-d')))->get();
        return apiResponse($skp_pegawai);
    }

    public function getKinerjaTersimpan()
    {
        $nip = auth('api')->user()->nip;
        $kinerja_tersimpan = Kinerja::where('nip', $nip)->where('tgl_mulai', date('Y-m-d'))->where('jenis_kinerja', 'hadir')->where('approve', '5')->first();

        try {
            $data = [
                'id' => $kinerja_tersimpan->id,
                'nip' => $kinerja_tersimpan->nip,
                'tgl_mulai' => $kinerja_tersimpan->tgl_mulai,
                'tgl_selesai' => $kinerja_tersimpan->tgl_selesai,
                'jenis_kinerja' => $kinerja_tersimpan->jenis_kinerja,
                'rincian_kinerja' => $kinerja_tersimpan->rincian_kinerja,
                'approve' => $kinerja_tersimpan->approve
            ];
            return $this->ApiSpecResponses($data);

        } catch (Exception $e) {
            return response()->json([
                'diagnostic' => [
                    'code' => '404',
                    'message' => 'Tidak ada kinerja yang tersimpan'
                ]
            ]);
        }
    }

    public function hapusKinerjaTersimpan($id)
    {
        $nip = auth('api')->user()->nip;
        $cek_kinerja = Kinerja::where('nip', $nip)->where('jenis_kinerja', 'hadir')->where('approve', '5')->where('tgl_mulai', date('Y-m-d'))->find($id);

        if ($cek_kinerja) {
            $cek_kinerja->delete();
            return response()->json([
                'diagnostic' => [
                    'code' => '201',
                    'message' => 'Berhasil menghapus draft'
                ]
            ]);
        } else {
            return response()->json([
                'diagnostic' => [
                    'code' => '404',
                    'message' => 'id tidak ditemukan'
                ]
            ]);
        }
    }

    public function inputKinerja(Request $request)
    {
        $input = $request->all();
        $nip = Auth::user()->nip;
        $res = $this->kinerja->inputKinerja($input, $nip);
        if ($res['data'] instanceof Kinerja) {
            if ($request->hasFile('doc')) {
                $this->kinerja->uploadFile($request->file('doc'), $res['data']->id);
                $res['data']->load('media');
            }
        }
        return apiResponse($res['data'], $res['diagnostic']);
    }

    public function tunjanganKinerja($bulan = null, $tahun = null)
    {
        $kinerja = new KinerjaRepository();
        $nip = auth('web')->user()->nip;
        $tunjangan = $kinerja->getTunjanganKinerja($nip, $bulan, $tahun);
        return apiResponse($tunjangan);
    }

    public function detailKinerja($tgl)
    {
        $result = (new KinerjaRepository())->getDetailKinerja($tgl);
        return apiResponse($result);
    }

    public function cekKinerja()
    {
        $pegawai = auth('api')->user();
        $nip = $pegawai->nip;
        $cek_kinerja = (new KinerjaRepository())->cekKinerja($nip);
        $true = true;
        $false = false;
        if ($cek_kinerja) {
            return apiResponse($true);
        } else {
            return apiResponse($false);
        }
    }
}
