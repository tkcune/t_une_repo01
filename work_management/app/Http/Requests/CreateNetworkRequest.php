<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Libraries\php\HeaderMessage;

class CreateNetworkRequest extends FormRequest
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
            'name' => 'required|max:256',
            'email' => 'required|max:256',
            'password' => 'required|max:256',
            'recieving_server' => 'required|max:256',
            'recieving_server_way' => 'required|max:1',
            'recieving_port_number' => 'required|max:6',
            'sending_server' => 'required|max:256',
            'sending_port_number' => 'required|max:6'
        ];
    }

    protected function failedValidation(Validator $validator){
        //入力項目が入っていない場合
        foreach($validator->failed() as $failed_row){
            if(array_key_exists('Required', $failed_row)){
                HeaderMessage::set_header_message('mnwer0004');
                throw new HttpResponseException(
                    back()->withInput($this->input)->withErrors($validator)
                );
            }
        }
        //入力項目は入っているが、何らかの入力エラーがある場合
        HeaderMessage::set_header_message('mnwer0003');
        throw new HttpResponseException(
            back()->withInput($this->input)->withErrors($validator)
        );
    }
}
