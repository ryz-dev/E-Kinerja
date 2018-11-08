<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckinoutRequests extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'userid' => 'required|integer',
            'checktime' => 'required',
            'checktype' => 'required|integer',
            'verifycode'=> 'required|integer',
            'sensorid'  => 'required|integer',
            'workcode'  => 'required|integer',
            'sn'        => 'required'
        ];

        return $rules;
    }
}
