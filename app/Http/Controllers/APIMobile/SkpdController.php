<?php

namespace App\Http\Controllers\APIMobile;

use App\Models\MasterData\Skpd;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;

class SkpdController extends ApiController
{
    public function listSkpd(Request $request)
    {
        $user = auth('api')->user();
        $page = $request->input('page');
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $skpd = Skpd::orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $skpd = $skpd->where('nama_skpd', 'like', '%' . $request->input('q') . '%')->orderBy('nama_skpd','asc');
            }

            if ($page) {
                $skpd = $skpd->paginate($this->show_limit_mobile);
            } else {
                $skpd = $skpd->get();
            }
            $data = [];

            /*if ($user->role()->first()->nama_role == 'Bupati') {
                $data[] = [
                    'id' => -1,
                    'uuid' => 'uuid',
                    'nama_skpd' => 'SEKERTARIS DAERAH',
                    'keterangan' => 'SEKERTARIS DAERAH',
                ];
            }*/

            foreach ($skpd as $s) {
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
}
