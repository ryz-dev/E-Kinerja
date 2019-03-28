<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $show_limit = 7;
    protected $show_limit_mobile = 20;
    protected $query = null;

    public function ApiSpecResponses($response,$status = 200){
        $paging = $this->paging($response);
        $data = [
            'response' => method_exists($response,'total') ? ($response->toArray())['data'] : $response,
            'diagnostic' => [
                'code' => $status,
                'status' => 'OK'
            ]
        ];
        if (isset($paging->total)){
            $data = array_merge($data,[
                'pagination' => $paging
            ]);
        }
        return response()->json($data,200);
    }

    public function paging($raw)
    {
      $object = new \stdClass;
      if (method_exists($raw,'total'))
          $object->total = $raw->total();
      if (method_exists($raw,'perPage'))
          $object->per_page = $raw->perPage();
      if (method_exists($raw,'currentPage'))
          $object->current_page = $raw->currentPage();
      if (method_exists($raw,'lastPage'))
          $object->last_page = $raw->lastPage();
      if (method_exists($raw,'firstItem'))
          $object->from = $raw->firstItem();
      if (method_exists($raw,'lastItem'))
          $object->to = $raw->lastItem();
      return $object;
    }

    public function ApiResponse($response){
      $data = array_merge($response,
        array('diagnostic' => [
          'code' => 200,
          'status' => 'HTTP_OK'
        ])
      );
      return response()->json($data,200);
    }

}
