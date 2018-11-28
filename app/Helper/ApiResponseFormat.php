<?php
/**
 * Created by PhpStorm.
 * User: alifdoco
 * Date: 2018-11-27
 * Time: 17:20
 */

namespace App\Helper;


class ApiResponseFormat {

    public function STAT_OK() {
        return 200;
    }

    public function STAT_REQUIRED() {
        return 201;
    }

    public function STAT_BAD_REQUEST() {
        return 400;
    }

    public function STAT_UNAUTHORIZED() {
        return 401;
    }

    public function STAT_NOT_FOUND() {
        return 404;
    }

    public function STAT_REQUEST_TIMEOUT() {
        return 408;
    }

    public function INTERNAL_SERVER_ERROR() {
        return 500;
    }

    public function STAT_SERVICE_UNAVAILABLE() {
        return 503;
    }

    public function formatResponseWithPages($status,$data,$code = 200, $page = null) {
        $response = "";
        $dgn = [
            'code'  => $code,
            'status' => $status,
        ];
        if ($code == 200) {
            //success
            if ($page == null) {
                $response = [
                    'diagnostic' => $dgn,
                    'response' => $data
                ];
            } else {
                $response = [
                    'diagnostic' => $dgn,
                    'pagination' => $page,
                    'response' => $data
                ];
            }
        } else if ($code == 201) {
            $response = [
                'diagnostic' => $dgn,
                'response' => $data
            ];
        } else {
            $response = [
                'diagnostic' => $dgn
            ];
        }
        return $response;
    }

}