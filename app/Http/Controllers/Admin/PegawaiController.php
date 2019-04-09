<?php

namespace App\Http\Controllers\Admin;

use App\Imports\PegawaiImport;
use App\Models\MasterData\Agama;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;
use App\Repositories\PegawaiRepository;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class PegawaiController extends AdminController
{
    protected $pegawai;

    public function __construct(PegawaiRepository $pegawai)
    {
        $this->pegawai = $pegawai;
    }

    public function index()
    {
        return view('layouts/admin/pegawai/index');
    }

    public function show($id)
    {
        $pegawai = Pegawai::with('jabatan', 'agama', 'skpd')->where('nip', $id)->orWhere('uuid', $id)->firstOrFail();
        return view('layouts.admin.pegawai.detail', compact('pegawai'));
    }

    public function edit($id)
    {
        $pegawai = Pegawai::with('jabatan', 'agama', 'skpd')->where('nip', $id)->orWhere('uuid', $id)->firstOrFail();
        $data_option = new stdClass();
        $data_option->agama = Agama::get();
        $data_option->jabatan = Jabatan::get();
        $data_option->skpd = Skpd::get();
        return view('layouts.admin.pegawai.edit', compact('pegawai', 'data_option'));
    }

    public function add()
    {
        $data_option = new stdClass();
        $data_option->agama = Agama::get();
        $data_option->jabatan = Jabatan::get();
        $data_option->skpd = Skpd::get();
        return view('layouts.admin.pegawai.add', compact('data_option'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->input(), $this->pegawai->required());
        if ($validation->fails()) {
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ], 422);
        }
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        if ($request->hasFile('foto')) {
            $input['foto'] = $this->pegawai->uploadFoto($request->file('foto'));
        }
        if ($data = $this->pegawai->create($input)) {
            $this->pegawai->setPassword($data->nip, 'secret');
            return $this->ApiSpecResponses($data);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal menambah pegawai'
        ], 500);
    }

    public function list(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $pegawai = $this->pegawai->with(['jabatan', 'agama', 'skpd'])->orderBy('created_at', 'DESC');
        $pegawai = $pegawai->search($request->query());
        return $this->ApiSpecResponses($pegawai);
    }

    public function detail($id)
    {
        if ($pegawai = $this->pegawai->with(['jabatan', 'agama', 'skpd'])->find($id)) {
            return $this->ApiSpecResponses($pegawai);
        }
        return $this->ApiSpecResponses([
            'message' => 'NOT_FOUND'
        ], 404);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->input(), $this->pegawai->required($id));
        if ($validation->fails()) {
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ], 422);
        }
        $update = $request->input();
        if ($request->hasFile('foto')) {
            $update['foto'] = $this->pegawai->uploadFoto($request->file('foto'));
        }
        if ($data = $this->pegawai->update($id, $update)) {
            return $this->ApiSpecResponses([
                'message' => 'Berhasil mengupdate pegawai'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal mengupdate pegawai'
        ], 500);
    }

    public function delete($id)
    {
        if ($this->pegawai->delete($id)) {
            return $this->ApiSpecResponses([
                'message' => 'Berhasil menghapus pegawai'
            ]);
        } else if ($this->pegawai->withTrashed()->delete($id, true)) {
            return $this->ApiSpecResponses([
                'message' => 'Berhasil menghapus pegawai'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal menghapus pegawai'
        ], 500);
    }

    public function getPage(Request $request)
    {
        $data = $this->pegawai->getPage($request->query());
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }

    public function getSkpd()
    {
        $skpd = Skpd::select('nama_skpd', 'id')->pluck('nama_skpd', 'id')->all();
        return $this->ApiSpecResponses($skpd);
    }

    public function deleted()
    {
        return view('layouts/admin/pegawai/deleted');
    }

    public function import(Request $request)
    {
        Excel::import(new PegawaiImport, $request->file('import'));
        return redirect()->back();
    }

    public function downloadRekapBulanan(Request $request)
    {
        $periode_rekap = $request->has('periode_rekqp') ? $request->periode_rekap : date('Y-m-d');
        $nip = Pegawai::where('id_jabatan',3)->first()->nip;
        $skpd = $request->has('id_skpd') ? $request->input('id_skpd') : 1;
        return $this->pegawai->downloadRekapBulanan($nip,1,$skpd,$periode_rekap);
    }

    public function updatePassword(Request $request)
    {
        $user = auth('web')->user();

        if ($request->input('oldPassword') === $request->input('newPassword')) {
            return $this->ApiSpecResponses([
                'message' => 'Kata sandi lama dan kata sandi baru tidak boleh sama!'
            ], 500);
        }

        if (Hash::check($request->input('oldPassword'), $user->password)) {
            if ($this->pegawai->updatePassword($user->nip, $request->input('newPassword'))) {
                return $this->ApiSpecResponses([
                    'message' => 'berhasil mengubah kata sandi'
                ]);
            }
        }
        return $this->ApiSpecResponses([
            'message' => 'Kata sandi lama salah!'
        ], 500);
    }

    public function restorePegawai($nip)
    {
        $this->pegawai->withTrashed()->where('nip', $nip)->restore();
    }
}
