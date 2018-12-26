<?php

namespace App\Http\Controllers\API;

use App\Models\Absen\Checkinout;
use App\Models\Absen\Etika;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

class KinerjaController extends ApiController
{
    public function getKinerjaTersimpan(){
        $kinerja_tersimpan = Kinerja::where('tgl_mulai',date('Y-m-d'))->where('jenis_kinerja','hadir')->where('approve','5')->firstOrFail();
        return $this->ApiSpecResponses($kinerja_tersimpan);
    }

    public function hapusKinerjaTersimpan($id){
        $nip = auth('web')->user()->nip;
        $cek_kinerja = Kinerja::where('nip',$nip)->where('jenis_kinerja','hadir')->where('approve','5')->where('tgl_mulai',date('Y-m-d'))->findOrFail($id);
        $cek_kinerja->delete();
        return response()->json([
            'message' => 'Berhasil menghapus draft'
        ]);
    }

    public function inputKinerja(Request $request){
        $input = $request->input();
        $input['nip'] = auth('web')->user()->nip;
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
                    $cek_hadir_kerja = Checkinout::whereDate('checktime',date('Y-m-d'))->where('checktype','0')->where('nip',$input['nip'])->first();
                    $cek_pulang_kerja = Checkinout::whereDate('checktime',date('Y-m-d'))->where('checktype','1')->where('nip',$input['nip'])->first();
                    /*if (strtotime($cek_hadir_kerja->checktime) <= strtotime(date('Y-m-d')." 09:00:00")){
                        if ((strtotime($cek_pulang_kerja->checktime) - strtotime($cek_hadir_kerja->checktime)) >= (8.5 * 3600)) {*/
                            if ($request->has('status')){
                                if ($request->input('status') == 5){
                                    $input['approve'] = 5;
                                }
                            }
                            if ($request->has('id') && $request->input('id')){
                                $kinerja = Kinerja::where('nip', $input['nip'])->where('jenis_kinerja','hadir')->findOrFail($request->input('id'));
                                $kinerja->update([
                                    'rincian_kinerja' => $input['rincian_kinerja'],
                                    'approve' => $input['approve']
                                ]);
                            } else {
                                $kinerja = Kinerja::create($input);
                            }
                            return $this->ApiSpecResponses($kinerja);
                        /*}
                    } else {
                        return response()->json([
                                'diagnostic' => [
                                    'code' => '403',
                                    'message' => 'gagal menambah kinerja, tidak tercata hadir pada absen'
                                ]
                            ]);
                    }*/
                }

                return response()->json([
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, bukan hari kerja'
                    ]
                ]);
            } else {
                $cek_kinerja = Kinerja::where('nip',$input['nip'])->where(function ($query)use($input){
                    $query->where(function ($query) use ($input){
                        $query->where('tgl_mulai','<=',$input['tgl_mulai']);
                        $query->where('tgl_selesai','>=',$input['tgl_mulai']);
                    });
                    $query->orWhere(function ($query)use($input){
                        $query->where('tgl_mulai','<=',$input['tgl_selesai']);
                        $query->where('tgl_selesai','>=',$input['tgl_selesai']);
                    });
                })->whereIn('approve',[5])->first();
                if ($cek_kinerja){
                    return response()->json([
                        'diagnostic' => [
                            'code' => '403',
                            'message' => 'gagal menambahkan kinerja, sdh ada kinerja yang disimpan untuk hari ini'
                        ]
                    ]);
                }
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
        $persen_kinerja =  FormulaVariable::where('variable','kinerja')->first()->persentase_nilai;
        $persen_etika =  FormulaVariable::where('variable','etika')->first()->persentase_nilai;

        $pegawai = auth('web')->user();
        $nip = $pegawai->nip;

        $pegawai->load('jabatan.golongan');
        $jumlah_tunjangan = $pegawai->jabatan->golongan->tunjangan;


        $hari_kerja = HariKerja::whereHas('statusHari',function ($query){
            $query->where('status_hari','kerja');
        })->where('bulan',$bulan)->where('tahun',$tahun)->orderBy('tanggal','asc')->get();
        $jumlah_hari = $hari_kerja->count();
        $jumlah_kinerja = $jumlah_etika = $absen = 0;
        $data_etika_kinerja = [];
        $etika = Etika::where('nip', $nip)->where('tanggal','like', $tahun."-".$bulan."%")->first();
        $jumlah_etika = $etika ? $etika->persentase : 0;
        if ($etika){
            $etika->tanggal_etika = ucfirst(Bulan::where('kode',$bulan)->first()->nama_bulan)." ".$tahun;
        }
        if ($jumlah_hari > 0) {
            foreach ($hari_kerja AS $hk) {
                $knj = Kinerja::where('nip', $nip)->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->terbaru();
//                $etk = Etika::where('nip', $nip)->where('tanggal', '=', $hk->tanggal)->first();
                $abs = Checkinout::where('nip', $nip)->whereDate('checktime', $hk->tanggal)->orderBy('checktype','asc')->get();
                $status = 'alpa';
                if ($abs->count() > 0) {
                    $in = false;
                    $out = false;
                    $masuk = $pulang = null;
                    foreach ($abs AS $a) {
                        if ($a->checktype == '0') {
                            $in = true;
                            $masuk = $a->checktime;
                        }
                        if ($a->checktype == '1') {
                            $out = true;
                            $pulang = $a->checktime;
                        }
                    }
                    if (strtotime($masuk) <= strtotime($hk->tanggal." 09:00:00") ){
                        if ((strtotime($pulang)-(strtotime($masuk))) >= (8.5 * 3600)){
                            $absen++;
                            $status = 'hadir';
                        }
                    }
                }
                $data_etika_kinerja[] = [
                    'tanggal' => $hk->tanggal,
                    'tanggal_string' => $this->formatDate($hk->tanggal),
                    'tanggal_string2' => $this->formatDate2($hk->tanggal),
                    'tanggal_etika' => $this->formatDate3($hk->tanggal),
                    'hari' => ucfirst($hk->Hari->nama_hari),
                    'kinerja' => $knj->first() ? $knj->first()->toArray() : null,
                    'etika' => $etika ? $etika->toArray() : null,
                    'absen' => $abs ? $abs->toArray() : null,
                    'status' => ucfirst($status)
                ];
                /*$etika[] = $etk ? $etk->toArray() : null;
                if ($etk) {
                    $jumlah_etika += $etk->persentase;
                }*/
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
//                'etika' => ($jumlah_etika / (100 * $jumlah_hari)) * 100
                'etika' => $jumlah_etika
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
            'pegawai' => $pegawai,
            'pencapaian' => [
                'absen' => $jumlah_hari > 0 ? $this->toDecimal($persentase['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toDecimal($persentase['kinerja']) : 0,
                'etika' => $jumlah_hari > 0 ? $this->toDecimal($persentase['etika']) : 0,
            ],
            'persentase' => [
                'absen' => $persen_absen,
                'kinerja' => $persen_kinerja,
                'etika' => $persen_etika,
            ],
            'total' => [
                'absen' => $jumlah_hari > 0 ? $this->toDecimal($persentase_total['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toDecimal($persentase_total['kinerja']) : 0,
                'etika' => $jumlah_hari > 0 ?  $this->toDecimal($persentase_total['etika']) : 0,
                'total' => $jumlah_hari > 0 ? $this->toDecimal($total_persentase_tunjangan) : 0
            ],
            'jumlah_tunjagan' => $jumlah_hari > 0 ? $this->toDecimal($jumlah_tunjangan) : 0,
            'total_tunjangan_diterima' => $jumlah_hari > 0 ? $this->toDecimal($total_tunjangan) : 0,
            'data' => $data_etika_kinerja
        ];
        return $this->ApiSpecResponses($response);
    }

    private function toDecimal($number){
        return number_format((float)$number,2,',','.');
    }
}
