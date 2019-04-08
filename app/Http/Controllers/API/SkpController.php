<?php

namespace App\Http\Controllers\API;


use App\Repositories\SkpRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SkpController extends ApiController
{
    protected $skp;

    public function __construct(SkpRepository $skp)
    {
        $this->skp = $skp;
    }

    public function listSkpTask(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skp = $this->skp->orderBy('id')->search($request->query(), $this->show_limit);
        return $this->ApiSpecResponses($skp);
    }

    public function detailSkpTask($id)
    {
        if ($skp = $this->skp->find($id)) {
            return $this->ApiSpecResponses($skp);
        }
        return $this->ApiSpecResponses([
            'message' => 'NOT_FOUND'
        ], 404);
    }

    public function storeSkpTask(Request $request)
    {
        $validation = Validator::make($request->input(), $this->skp->required());
        if ($validation->fails()) {
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ], 422);
        }
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        if ($skp = $this->skp->create($input)) {
            return $this->ApiSpecResponses($skp);
        }
        return $this->ApiSpecResponses([
            'message' => 'gagal menyimpan skp'
        ], 500);
    }

    public function updateSkp(Request $request, $id)
    {
        $validation = Validator::make($request->input(), $this->skp->required());
        if ($validation->fails()) {
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ], 422);
        }
        $update = $request->input();

        if ($this->skp->update($id, $update)) {
            return $this->ApiSpecResponses([
                'message' => 'berhasil mengupdate skp'

            ], 500);
        }
        return $this->ApiSpecResponses([
            'message' => 'gagal mengupdate skp'
        ]);

    }

    public function deleteSkp($id)
    {

        if ($this->skp->delete($id)) {
            return $this->ApiSpecResponses([
                'status' => 200,
                'message' => 'skp task berhasil dihapus'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'gagal menghapus skp task'
        ], 500);
    }

    public function getPageSkp(Request $request)
    {
        $data = $this->skp->getPage($request->has('q') ? $request->q : []);
        $data = ceil($data / $this->show_limit);
        return $this->ApiSpecResponses([
            'halaman' => $data
        ]);
    }

}
