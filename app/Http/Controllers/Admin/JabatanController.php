<?php

namespace App\Http\Controllers\Admin;

use App\Models\MasterData\Golongan;
use App\Models\MasterData\Jabatan;
use App\Repositories\JabatanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class JabatanController extends AdminController
{
    protected $jabatan;

    public function __construct(JabatanRepository $jabatan)
    {
        $this->jabatan = $jabatan;
    }

    public function index(Request $request)
    {
        return view('layouts.admin.jabatan.index');
    }

    public function show($id)
    {
        $jabatan = Jabatan::with('atasan', 'golongan')->where('id', $id)->orWhere('uuid', $id)->firstOrFail();
        return view('layouts.admin.jabatan.detail', compact('jabatan'));
    }

    public function add()
    {
        $data_option = new stdClass();
        $data_option->golongan = Golongan::get();
        $data_option->jabatan = Jabatan::get();
        return view('layouts.admin.jabatan.add', compact('data_option'));
    }

    public function edit($id)
    {
        $jabatan = Jabatan::with('atasan', 'golongan')->where('id', $id)->orWhere('uuid', $id)->firstOrFail();
        $data_option = new stdClass();
        $data_option->golongan = Golongan::get();
        $data_option->jabatan = Jabatan::get();
        return view('layouts.admin.jabatan.edit', compact('jabatan', 'data_option'));
    }

    public function list(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $jabatan = $this->jabatan->with(['golongan', 'atasan'])->orderBy('id')->search($request->query(), $this->show_limit);
        return $this->ApiSpecResponses($jabatan);
    }

    public function detail($id)
    {
        if ($jabatan = $this->jabatan->with(['golongan', 'atasan'])->find($id)) {
            return $this->ApiSpecResponses($jabatan);
        }
        return $this->ApiSpecResponses([
            'message' => 'NOT_FOUND'
        ], 404);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->input(), $this->jabatan->required());
        if ($validation->fails()) {
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ], 422);
        }
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        if ($jabatan = $this->jabatan->create($input)) {
            return $this->ApiSpecResponses($jabatan);
        }
        return $this->ApiSpecResponses([
            'message' => 'gagal menyimpan jabatan'
        ], 500);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->input(), $this->jabatan->required());
        if ($validation->fails()) {
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ], 422);
        }
        $update = $request->input();
        if ($this->jabatan->update($id, $update)) {
            return $this->ApiSpecResponses([
                'message' => 'berhasil mengupdate jabatan'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal mengupdate data jabatan'
        ], 500);
    }

    public function delete($id)
    {
        if ($this->jabatan->delete($id)) {
            return $this->ApiSpecResponses([
                'message' => 'jabatan pegawai berhasil dihapus'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'skp pegawai gagal dihapus'
        ], 500);
    }

    public function getPage(Request $request)
    {
        $data = $this->jabatan->getPage($request->query());
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
