<?php

namespace App\Http\Controllers\API;


use App\Models\Skp;
use App\Models\SkpPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkpController extends ApiController
{
    public function listSkpTask(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skp = Skp::orderBy('id','desc');
        if ($request->has('q')) {
            $skp = $skp->where('task','like','%'.$request->input('q').'%');
        }
        $skp = $skp->paginate($this->show_limit);
        return $this->ApiSpecResponses($skp);
    }

    public function listSkpPegawai(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skp_pegawai = SkpPegawai::with('pegawai','skpTask','atasanUpdate')->orderBy('id','desc');
        if ($request->has('q')){
            $q = $request->input('q');
            $skp_pegawai = $skp_pegawai->whereHas('pegawai',function($query)use($q){
                $query->where('nama','like','%'.$q.'%');
            })
            ->orWhereHas('skpTask',function($query)use($q){
                $query->where('task','like','%'.$q.'%');
            })
            ->orWhereHas('atasanUpdate',function($query)use($q){
                $query->where('nama','like','%'.$q.'%');
            })
            ->orWhere('nip_pegawai','like','%'.$q.'%')
            ->orWhere('nip_updtae','like','%'.$q.'%');
        }
        $skp_pegawai = $skp_pegawai->paginate($this->show_limit);
        return $this->ApiSpecResponses($skp_pegawai);
    }

    public function detailSkpTask($id){
        try {
            $skp = Skp::findOrFail($id);
        } catch (\Exception $e){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
        return $this->ApiSpecResponses($skp);
    }

    public function detailSkpPegawai($id){
        try {
            $skp_pegawai = SkpPegawai::with('pegawai','skpTask','atasanUpdate')->findOrFail($id);
        } catch (\Exception $e){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
        return $this->ApiSpecResponses($skp_pegawai);
    }

    public function storeSkpTask(Request $request){
        $this->validate($request,[
            'task' => 'required'
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $skp = Skp::create($input);
        return $this->ApiSpecResponses($skp);
    }

    public function storeSkpPegawai(Request $request){
        $this->validate($request,[
            'nip_pegawai' => 'required',
            'id_skp' => 'required',
            'periode' => 'date',
            'status' => '',
            'tanggal_selesai' => '',
            'nip_update' => ''
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        try {
            $skp_pegawai = SkpPegawai::create($input);
        } catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ],500);
        }
        return $this->ApiSpecResponses($skp_pegawai);
    }

    public function updateSkp(Request $request,$id){
        try {
            $skp = Skp::findOrFail($id);
        }catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
        $this->validate($request,[
            'task' => 'required'
        ]);
        $update = $request->input();
        try {
            $skp->update($update);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
        return $this->ApiSpecResponses($skp);
    }

    public function updateSkpPegawai(Request $request,$id){
        try {
            $skp_pegawai = SkpPegawai::findOrFail($id);
        }catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
        $this->validate($request,[
            'nip_pegawai' => '',
            'id_skp' => '',
            'periode' => 'date',
            'status' => 'required',
            'tanggal_selesai' => '',
            'nip_update' => ''
        ]);
        $update = $request->input();
        try {
            $skp_pegawai->update($update);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
        return $this->ApiSpecResponses($skp_pegawai);
    }

    public function deleteSkp($id){
        try {
            $skp = Skp::findOrFail($id);
        }catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
        try {
            $skp->delete($id);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
        return response()->json([
            'status' => 200,
            'message' => 'skp task berhasil dihapus'
        ]);
    }

    public function deleteSkpPegawai($id){
        try {
            $skp_pegawai = SkpPegawai::findOrFail($id);
        }catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
        try {
            $skp_pegawai->delete($id);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
        return response()->json([
            'status' => 200,
            'message' => 'skp pegawai berhasil dihapus'
        ]);
    }

    public function getPageSkp(Request $request)
    {
        if ($request->has('q')) {
            $data = Skp::where('task','like','%'.$request->input('q').'%')->count();
        } else {
            $data = Skp::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }

    public function getPageSkpPegawai(Request $request)
    {
        if ($request->has('q')) {
            $q = $request->input('q');
            $data = SkpPegawai::whereHas('pegawai',function($query)use($q){
                    $query->where('nama','like','%'.$q.'%');
                })
                ->orWhereHas('skpTask',function($query)use($q){
                    $query->where('task','like','%'.$q.'%');
                })
                ->orWhereHas('atasanUpdate',function($query)use($q){
                    $query->where('nama','like','%'.$q.'%');
                })
                ->orWhere('nip_pegawai','like','%'.$q.'%')
                ->orWhere('nip_updtae','like','%'.$q.'%')
                ->count();
        } else {
            $data = SkpPegawai::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }

}
