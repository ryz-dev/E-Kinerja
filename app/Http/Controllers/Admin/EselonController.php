<?php

namespace App\Http\Controllers\Admin;

use App\Models\MasterData\Eselon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EselonController extends AdminController
{
    public function index(Request $request)
    {
        return view('layouts.admin.eselon.index');
    }

    public function show($id)
    {
        $eselon = Eselon::where('id', $id)->orWhere('uuid', $id)->firstOrFail();
        return view('layouts.admin.eselon.detail', compact('eselon'));
    }

    public function add()
    {
        return view('layouts.admin.eselon.add');
    }

    public function edit($id)
    {
        $eselon = Eselon::where('id', $id)->orWhere('uuid', $id)->firstOrFail();
        return view('layouts.admin.eselon.edit', compact('eselon'));
    }


    public function store(Request $request, $json = true)
    {
        $this->validate($request, [
            'eselon' => 'required',
            'tunjangan' => 'required'
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $agama = Eselon::create($input);
        if ($json)
            return response()->json($agama->toArray());
        return $agama;
    }

    public function update(Request $request, $id, $json = true)
    {
        $this->validate($request, [
            'eselon' => 'required',
            'tunjangan' => 'required'
        ]);
        $eselon = Eselon::where('uuid', $id)->firstOrFail();
        $eselon->update($request->input());
        if ($json)
            return response()->json($eselon->toArray());
        return $eselon;
    }

    public function delete($id, $json = true)
    {
        $eselon = Eselon::where('uuid', $id)->firstOrFail();
        try {
            $eselon->delete();
        } catch (Exception $exception) {
        }
        if ($json)
            return response()->json([
                'message' => 'berhasil menghapus data'
            ]);
        return [
            'message' => 'berhasil menghapus data'
        ];
    }
}
