<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Libraries\php\Service\HeaderMessage;
use App\Rules\DomainRule;
use App\Facades\OutputLog;

class SendTestMailRequest extends FormRequest
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
            'test_email' => ['required', new DomainRule]
        ];
    }

    protected function failedValidation(Validator $validator){
        
        //メール送信が失敗
        OutputLog::message_log(__FUNCTION__, 'mmnwer0001');
        HeaderMessage::set_header_message('mmnwer0001');
        throw new HttpResponseException(
            redirect()->route('psnw01.index')->withInput($this->input)
        );
    }
}
