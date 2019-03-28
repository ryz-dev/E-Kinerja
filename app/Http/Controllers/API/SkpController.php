<?php

namespace App\Http\Controllers\API;


use App\Repositories\SkpRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkpController extends ApiController
{
    protected $skp;
    public function __construct(SkpRepository $skp)
    {
        $this->skp = $skp;
    }

    public function listSkpTask(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        if ($request->has('q')) {
            $skp = $this->skp->orderBy('id')->search(['q' => $request->q],$this->show_limit);
        } else {
            $skp = $this->skp->orderBy('id')->paginate($this->show_limit);
        }
        return $this->ApiSpecResponses($skp);
    }

    public function detailSkpTask($id){
        $skp = $this->skp->find($id);
        return $this->ApiSpecResponses($skp);
    }

    public function storeSkpTask(Request $request){
        $this->validate($request,[
            'task' => 'required'
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $skp = $this->skp->create($input);
        return $this->ApiSpecResponses($skp);
    }

    public function updateSkp(Request $request,$id){
        $this->validate($request,[
            'task' => 'required'
        ]);
        $update = $request->input();
        try {
            $skp = $this->skp->update($id,$update);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
        return $this->ApiSpecResponses($skp);
    }

    public function deleteSkp($id){

        try {
            $this->skp->delete($id);
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

    public function getPageSkp(Request $request)
    {
        $data = $this->skp->getPage($request->has('q') ? $request->q : []);
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }

}
