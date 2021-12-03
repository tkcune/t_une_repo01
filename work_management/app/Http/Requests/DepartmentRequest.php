<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Libraries\php\OutputLog;
use App\Libraries\php\Message;

class DepartmentRequest extends FormRequest
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
        return [
            'name' =>'required',
            'status'=>'required',
            'management_number'=>'required',
        ];
    }

    public function messages()
    {

        OutputLog::message_log(__FUNCTION__, 'mhcmer0003','01');

        return [
            'name.required'=>'入力されてない項目があります。',
            'status.required'=>'入力されてない項目があります。',
            'management_number.required'=>'入力されてない項目があります。'
        ];
    }
}
