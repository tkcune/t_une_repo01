<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Libraries\php\OutputLog;
use App\Libraries\php\Message;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        return [
            'name.required'=>'名前を入力してください',
            'status.required'=>'状態を入力してください',
            'management_number.required'=>'管理者番号を入力してください'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        OutputLog::message_log(__FUNCTION__, 'mhcmer0003','01');
        $message = Message::get_message('mhcmer0003',[0=>'']);
        session(['message'=>$message[0]]);
        $this->merge(['validated' => 'true']);
        // リダイレクト先
        throw new HttpResponseException(
        back()->withInput($this->input)->withErrors($validator)
        );
    }

}