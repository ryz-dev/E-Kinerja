<?php

if (!function_exists('month')) {
<<<<<<< HEAD
    function month($date){
        return (int)date('m', strtotime($date));
=======
    function month($date)
    {
        return date('m', strtotime($date));
>>>>>>> c4541c7e588da8e762e8866369bc82e8f612d593
    }
}

if (!function_exists('year')) {
<<<<<<< HEAD
    function year($date){
        return (int)date('Y', strtotime($date));
=======
    function year($date)
    {
        return date('Y', strtotime($date));
>>>>>>> c4541c7e588da8e762e8866369bc82e8f612d593
    }
}

if (!function_exists('namaBulan')) {
    function namaBulan($int)
    {
        $nama = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return $nama[(int)$int];
    }
}
if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        return date('d/m/Y', strtotime($date));
    }
}if (!function_exists('formatDate2')) {
    function formatDate2($date)
    {
        $d = date('d', strtotime($date));
        $bulan = (int)date('m', strtotime($date));
        $m = namaBulan($bulan);
        $y = date('Y', strtotime($date));
        return $d . " " . $m . " " . $y;
    }
}
if (!function_exists('formatDate3')) {
    function formatDate3($date)
    {
        $bulan = (int)date('m', strtotime($date));
        $m = namaBulan($bulan);
        $y = date('Y', strtotime($date));
        return $m . " " . $y;
    }
}
if (!function_exists('apiReponse')) {
    function apiResponse($response, $diagnostic = [], $status = 200)
    {
        $paging = paging($response);
        $data = [
            'response' => method_exists($response, 'total') ? ($response->toArray())['data'] : $response,
            'diagnostic' => [
                'code' => $status,
                'status' => 'OK'
            ]
        ];
        if (!empty($diagnostic)) {
            $data['diagnostic'] = $diagnostic;
        }
        if (isset($paging->total)) {
            $data = array_merge($data, [
                'pagination' => $paging
            ]);
        }
        return response()->json($data, 200);
    }
}
if (!function_exists('paging')) {
    function paging($raw)
    {
        $object = new stdClass;
        if (method_exists($raw, 'total'))
            $object->total = $raw->total();
        if (method_exists($raw, 'perPage'))
            $object->per_page = $raw->perPage();
        if (method_exists($raw, 'currentPage'))
            $object->current_page = $raw->currentPage();
        if (method_exists($raw, 'lastPage'))
            $object->last_page = $raw->lastPage();
        if (method_exists($raw, 'firstItem'))
            $object->from = $raw->firstItem();
        if (method_exists($raw, 'lastItem'))
            $object->to = $raw->lastItem();
        return $object;
    }
}
