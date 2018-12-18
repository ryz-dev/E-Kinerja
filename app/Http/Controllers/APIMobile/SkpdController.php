<?php

namespace App\Http\Controllers\APIMobile;
use App\Models\MasterData\Skpd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class SkpdController extends ApiController
{
    public function listSkpd(Request $request)
    {
        $page = $request->input('page');
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $skpd = Skpd::orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $skpd = $skpd->where('nama_skpd','like','%'.$request->input('q').'%');
            }

            if ($page) {
                $skpd = $skpd->paginate($this->show_limit_mobile);
            } else {
                $skpd = $skpd->get();
            }
            
            $data = [];
            foreach($skpd as $s){
                $data[] = [
                    'id' => $s->id,
                    'uuid' => $s->uuid,
                    'nama_skpd' => $s->nama_skpd,
                    'keterangan' => $s->keterangan,
                ];
            }

            return $this->ApiSpecResponses($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function getPage(Request $request)
    {
        if ($request->has('q')) {
            $data = Skpd::where('nama_skpd','like','%'.$request->input('q').'%')
                ->count();
        } else {
            $data = Skpd::count();
        }
        $data = ceil($data / $this->show_limit_mobile);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
