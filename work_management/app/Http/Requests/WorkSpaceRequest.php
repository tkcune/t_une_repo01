<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\OutputLog;
use App\Libraries\php\Service\Message;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\JapaneseAndAlphaNumRule;
use App\Rules\StatusRule;
use App\Rules\PersonnelRule;

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
            // JapaneseAndAlphaNumRule：日本語、英数字のバリデーションファイル
            'name' => ['required', new JapaneseAndAlphaNumRule],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '作業場所名を入力してください',

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
        } elseif ($validator->errors()->first('status') == "不正な入力が行われました" or $validator->errors()->first('responsible_person_id') == "不正な入力が行われました") {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0013', '01');
            $message = Message::get_message_handle('mhcmer0013', [0 => '']);
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
