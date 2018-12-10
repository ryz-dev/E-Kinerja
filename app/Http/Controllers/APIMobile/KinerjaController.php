<?php

namespace App\Http\Controllers\APIMobile;

use App\Models\Absen\Checkinout;
use App\Models\Absen\Etika;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

class KinerjaController extends ApiController
{
    public function inputKinerja(Request $request){
        $input = $request->input();
        $input['nip'] = auth('api')->user()->nip;
        if (in_array($input['jenis_kinerja'],['hadir','sakit'])){
            $input['tgl_mulai'] = date('Y-m-d');
            $input['tgl_selesai'] = date('Y-m-d');
        } else {
            $tgl_mulai = explode('/',$input['tgl_mulai']);
            $tgl_selesai = explode('/',$input['tgl_selesai']);

            $input['tgl_mulai'] = $tgl_mulai[2].'-'.$tgl_mulai[0].'-'.$tgl_mulai[1];
            $input['tgl_selesai'] = $tgl_selesai[2].'-'.$tgl_selesai[0].'-'.$tgl_selesai[1];
            if (strtotime($input['tgl_mulai']) > strtotime($input['tgl_selesai'])){
                return response()->json([
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, tanggal berakhir lebih kecil dari tanggal mulai'
                    ]
                ]);
            }
        }
//        $cek_kinerja = Kinerja::where('nip',$input['nip'])->where('tgl_mulai','<=',$input['tgl_mulai'])->where('tgl_selesai','>=',$input['tgl_selesai'])->whereIn('approve',[0,2])->first();
        $cek_kinerja = Kinerja::where('nip',$input['nip'])->where(function ($query)use($input){
            $query->where(function ($query) use ($input){
                $query->where('tgl_mulai','<=',$input['tgl_mulai']);
                $query->where('tgl_selesai','>=',$input['tgl_mulai']);
            });
            $query->orWhere(function ($query)use($input){
                $query->where('tgl_mulai','<=',$input['tgl_selesai']);
                $query->where('tgl_selesai','>=',$input['tgl_selesai']);
            });
        })->whereIn('approve',[0,2])->first();
        if (!$cek_kinerja){
            $input['approve'] = 0;
            if ($input['jenis_kinerja'] == 'hadir'){
                $cek_hari_kerja = HariKerja::whereDate('tanggal',date('Y-m-d'))->first();
                if ($cek_hari_kerja){
                    $cek_hadir_kerja = Checkinout::whereDate('checktime',date('Y-m-d'))->where('checktype','1')->where('nip',$input['nip'])->first();
                    if ($cek_hadir_kerja){
                        $kinerja = Kinerja::create($input);
                        return $this->ApiSpecResponses($kinerja);
                    } else {
                        return response()->json([
                                'diagnostic' => [
                                    'code' => '403',
                                    'message' => 'gagal menambah kinerja, tidak tercatat hadir pada absen'
                                ]
                            ]);
                    }
                }
                return response()->json([
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, bukan hari kerja'
                    ]
                ]);
            } else {
                $kinerja = Kinerja::create($input);
                return $this->ApiSpecResponses($kinerja);
            }
        } else {
            return response()->json([
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'gagal menambahkan kinerja, sdh ada kinerja pada hari yang sama untuk user ini'
                ]
            ]);
        }
    }

    public function tunjanganKinerja($bulan = null,$tahun = null){
        $bulan = (int)($bulan ? $bulan : date('m'));
        $tahun = $tahun ? $tahun : date('Y');

        $persen_absen = FormulaVariable::where('variable','absen')->first()->persentase_nilai;
        $persen_kinerja = FormulaVariable::where('variable','kinerja')->first()->persentase_nilai;
        $persen_etika = FormulaVariable::where('variable','etika')->first()->persentase_nilai;

        $pegawai = auth('api')->user();
        $nip = $pegawai->nip;

        $pegawai->load('jabatan.golongan');
        $jumlah_tunjangan = $pegawai->jabatan->golongan->tunjangan;

        $min_date = HariKerja::whereHas('statusHari', function ($query){
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        $hari_kerja = HariKerja::whereHas('statusHari',function ($query){
            $query->where('status_hari','kerja');
        })->where('bulan',$bulan)->where('tahun',$tahun)->get();
        $jumlah_hari = $hari_kerja->count();
        $jumlah_kinerja = $jumlah_etika = $absen = 0;
        $data_etika_kinerja = [];
        if ($jumlah_hari > 0) {
            foreach ($hari_kerja AS $hk) {
                $knj = Kinerja::where('nip', $nip)->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal);
                $etk = Etika::where('nip', $nip)->where('tanggal', '=', $hk->tanggal)->first();
                $abs = Checkinout::where('nip', $nip)->whereDate('checktime', $hk->tanggal)->get();
                if ($abs->count() > 0) {
                    $in = false;
                    $out = false;
                    foreach ($abs AS $a) {
                        if ($a->checktype == '0') {
                            $in = true;
                        }
                        if ($a->checktype == '1') {
                            $out = true;
                        }
                    }
                    if ($in && $out) {
                        $absen++;
                    }
                }
                $data_etika_kinerja[] = [
                    'tanggal' => $hk->tanggal,
                    'hari' => ucfirst($hk->Hari->nama_hari),
                    'approve' => $knj->first() ? $knj->first()->approve : 0,
                    'etika' => $etk ? $etk->persentase : 0,
                    'absen' => $knj->first() ? $knj->first()->jenis_kinerja : ""
                ];
                $etika[] = $etk ? $etk->toArray() : null;
                if ($etk) {
                    $jumlah_etika += $etk->persentase;
                }
                if ($knj->where('approve', 2)->first()) {
                    $jumlah_kinerja++;
                }
                if ($knj->where('jenis_kinerja', '<>', 'hadir')->where('approve', 2)->first()) {
                    $absen++;
                }
            }
            $persentase = [
                'absen' => ($absen / $jumlah_hari) * 100,
                'kinerja' => ($jumlah_kinerja / $jumlah_hari) * 100,
                'etika' => ($jumlah_etika / (100 * $jumlah_hari)) * 100
            ];
            $persentase_total = [
                'absen' => $persentase['absen'] * $persen_absen / 100,
                'kinerja' => $persentase['kinerja'] * $persen_kinerja / 100,
                'etika' => $persentase['etika'] * $persen_etika / 100
            ];
            $total_persentase_tunjangan = 0;
            foreach ($persentase_total AS $key => $value) {
                $total_persentase_tunjangan += $value;
            }
            $total_tunjangan = ($total_persentase_tunjangan * $jumlah_tunjangan) / 100;
        }
        $response = [
            'pencapaian' => [
                'absen' => $jumlah_hari > 0 ? $this->toFloat($persentase['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toFloat($persentase['kinerja']) : 0,
                'etika' => $jumlah_hari > 0 ? $this->toFloat($persentase['etika']) : 0,
            ],
            'persentase' => [
                'absen' => $persen_absen,
                'kinerja' => $persen_kinerja,
                'etika' => $persen_etika,
            ],
            'total' => [
                'absen' => $jumlah_hari > 0 ? $this->toFloat($persentase_total['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toFloat($persentase_total['kinerja']) : 0,
                'etika' => $jumlah_hari > 0 ?  $this->toFloat($persentase_total['etika']) : 0,
                'total' => $jumlah_hari > 0 ? $this->toFloat($total_persentase_tunjangan) : 0
            ],
            'jumlah_tunjagan' => $jumlah_hari > 0 ? $this->toDecimal($jumlah_tunjangan) : 0,
            'total_tunjangan_diterima' => $jumlah_hari > 0 ? $this->toDecimal($total_tunjangan) : 0,
            'data' => $data_etika_kinerja,
            'min_date' => $min_date->tanggal
        ];
        return $this->ApiSpecResponses($response);
    }

    public function detailKinerja($tgl) {
        $date = new HariKerja;

        /* Tarik tanggal sebelumnya */
        $date_prev = $date->whereDate('tanggal','<',$tgl)
        ->whereIdStatusHari(1)
        ->orderBy('tanggal','desc')
        ->first();

        /* Tarik tanggal setelahnya */
        $date_next = $date->whereDate('tanggal','>',$tgl)
        ->whereIdStatusHari(1)
        ->orderBy('tanggal','asc')
        ->first();

        $min_date = HariKerja::whereHas('statusHari', function ($query){
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        /* Data kinerja */
        $pegawai = auth('api')->user();
        $kinerja = Kinerja::where('nip',$pegawai->nip)
        ->whereDate('tgl_mulai','<=',$tgl)
        ->whereDate('tgl_selesai','>=',$tgl)
            ->terbaru()
            ->first();

        /* Data etika */
        $etika = Etika::where("nip",$pegawai->nip)
        ->select('persentase', 'keterangan')
        ->where("tanggal",$tgl)
        ->first();

        /* Data checkinout */
        $checkinout = Checkinout::where("nip",$pegawai->nip)
        ->select('checktime')
        ->whereDate("checktime",$tgl)
        ->get();

        /* Data array */
        $result = [
            'uuid' => $pegawai->uuid,
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'foto' => $pegawai->foto,
            'kinerja' => $kinerja ? $kinerja : [],
            'etika' => $etika ? $etika : [],
            'checkinout' => [
                    'in' => (count($checkinout)) ? $checkinout[0]->checktime : "",
                    'out' => (count($checkinout) > 1) ? $checkinout[1]->checktime : "",
                ],
            'min_date' => $min_date->tanggal
        ];

        return $this->ApiSpecResponses($result);
    }

    public function cekKinerja(){
        $pegawai = auth('api')->user();
        $nip = $pegawai->nip;
        $cek_kinerja = Kinerja::where('nip',$nip)->where(function ($query){
            $query->where(function ($query) {
                $query->where('tgl_mulai','<=',date('Y-m-d'));
                $query->where('tgl_selesai','>=',date('Y-m-d'));
            });
            $query->orWhere(function ($query){
                $query->where('tgl_mulai','<=',date('Y-m-d'));
                $query->where('tgl_selesai','>=',date('Y-m-d'));
            });
        })->whereIn('approve',[0,2])->first(); 

        $true = true;
        $false = false;
        if ($cek_kinerja) {
            return $this->ApiSpecResponses($true);
        } else{
            return $this->ApiSpecResponses($false);
        }
    }

    private function toDecimal($number){
        return number_format((float)$number,2,',','.');
    }

    private function toFloat($number){
        return (float)number_format((float)$number,2,'.',',');
    }
}
