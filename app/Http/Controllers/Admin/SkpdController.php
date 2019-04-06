<?php

namespace App\Http\Controllers\Admin;

use App\Models\MasterData\Skpd;
use App\Repositories\SkpdRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SkpdController extends AdminController
{
    protected $skpd;
    public function __construct(SkpdRepository $skpd)
    {
        $this->skpd = $skpd;
    }

    public function index(){
        return view('layouts/admin/skpd/index');
    }

    public function show($id){
        $skpd = Skpd::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.skpd.detail',compact('skpd'));
    }

    public function edit($id){
        $skpd = Skpd::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.skpd.edit',compact('skpd'));
    }

    public function add(){
        return view('layouts.admin.skpd.add');
    }

    public function list(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skpd = $this->skpd->search($request->query(),$this->show_limit);
        return $this->ApiSpecResponses($skpd);
    }

    public function detail($id){
        if ($skpd = $this->skpd->find($id)){
            return $this->ApiSpecResponses($skpd);
        }
        return $this->ApiSpecResponses([
            'message' => 'NOT_FOUND'
        ], 404);
    }

    public function store(Request $request){
        $validation = Validator::make($request->input(),$this->skpd->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        if ($skpd = $this->skpd->create($input)){
            return $this->ApiSpecResponses($skpd);
        }
        return $this->ApiSpecResponses([
            'message' => 'gagal menyimpan skpd'
        ],500);
    }

    public function update(Request $request,$id){
        $validation = Validator::make($request->input(),$this->skpd->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $update = $request->input();

        if ($this->skpd->update($id,$update)){
            return $this->ApiSpecResponses([
                'message' => 'berhasil mengupdate skpd'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal mengupdate data skpd'
        ], 500);
    }

    public function delete($id){
        if ($this->skpd->delete($id)){
            return $this->ApiSpecResponses([
                'message' => 'skpd pegawai berhasil dihapus'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'skpd pegawai gagal dihapus'
        ],500);
    }

    public function getPage(Request $request)
    {
        $data = $this->skpd->getPage($request->query());
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
