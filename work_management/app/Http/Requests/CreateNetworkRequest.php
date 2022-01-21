<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Libraries\php\Service\HeaderMessage;
use App\Rules\DomainRule;
use App\Facades\OutputLog;

//ネットワーク設定画面からdcnw01のデータベースに保存するデータをバリデーションチェック
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
            'name' => ['required', 'max:256'],
            'email' => ['required', 'max:256', 'email'],
            'password' => ['required', 'max:256'],
            'recieving_server' => ['required', 'max:256', new DomainRule],
            'recieving_server_way' => ['required', 'integer', 'between:0, 1'],
            'recieving_port_number' => ['required', 'integer', 'between:0, 65535'],
            'sending_server' => ['required', 'max:256', new DomainRule],
            'sending_port_number' => ['required', 'integer', 'between:0, 65535']
        ];
    }

    protected function failedValidation(Validator $validator){
        //入力項目が入っていない場合
        foreach($validator->failed() as $failed_row){
            if(array_key_exists('Required', $failed_row)){
                OutputLog::message_log(__FUNCTION__, 'mmnwer0004');
                HeaderMessage::set_header_message('mmnwer0004');
                throw new HttpResponseException(
                    back()->withInput($this->input)->withErrors($validator)
                );
            }
        }
        //入力項目は入っているが、何らかの入力エラーがある場合
        OutputLog::message_log(__FUNCTION__, 'mmnwer0003');
        HeaderMessage::set_header_message('mmnwer0003');
        throw new HttpResponseException(
            back()->withInput($this->input)->withErrors($validator)
        );
    }
}
