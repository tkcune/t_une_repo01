<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\OutputLog;
use Illuminate\Http\Request;
use App\Libraries\php\Service\Message;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\JapaneseAndAlphaNumRule;

class WorkSpaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 作業場所のバリテーションファイル
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
            'name' => ['required', new JapaneseAndAlphaNumRule],
            'postcode' => 'required',
            'prefectural' => 'required',
            'address' => 'required',
            'URL' => 'active_url|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '作業場所名を入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'prefectural.required' => '都道府県を入力してください',
            'address.required' => '市区町村を入力してください',
            'URL.max' => 'URLの文字数は255文字までです'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->errors()->first('name') == "英数字、ひらがな、カタカナ、漢字で入力してください") {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0012', '01');
            $message = Message::get_message_handle('mhcmer0012', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            // リダイレクト先
            throw new HttpResponseException(
                back()->withInput($this->input)->withErrors($validator)
            );
        } elseif ($validator->errors()->first('URL') == "URLの文字数は255文字までです") {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0015', '01');
            $message = Message::get_message_handle('mhcmer0015', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            // リダイレクト先
            throw new HttpResponseException(
                back()->withInput($this->input)->withErrors($validator)
            );
        } else {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0003', '01');
            $message = Message::get_message_handle('mhcmer0003', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            $this->merge(['validated' => 'true']);
            // リダイレクト先
            throw new HttpResponseException(
                back()->withInput($this->input)->withErrors($validator)
            );
        }
    }
}
