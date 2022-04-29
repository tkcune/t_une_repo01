<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\OutputLog;
use Illuminate\Http\Request;
use App\Libraries\php\Service\Message;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\JapaneseAndAlphaNumRule;
use App\Rules\PostRule;

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
    public function rules(Request $request)
    {
        if (isset($request->space_id)) {
            return [
                // 編集画面のバリデーション
                'name' => 'required',
                'postcode' => ['required', new PostRule],
                'prefectural' =>  'required',
                'address' => 'required',
                'URL' => ['active_url', 'nullable', 'max:255'],
            ];
        } else {
            // 登録画面のバリデーション
            return [
                'name' => ['required', new JapaneseAndAlphaNumRule],
                'postcode' => ['required', new PostRule],
                'prefectural' =>  ['required', new JapaneseAndAlphaNumRule],
                'address' => ['required', new JapaneseAndAlphaNumRule],
                'URL' => ['active_url', 'nullable', 'max:255'],
            ];
        }
    }

    public function messages()
    {
        return [
            'name.required' => '作業場所名を入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'prefectural.required' => '都道府県は5文字以内で入力してください',
            'address.required' => '市区町村を入力してください',
            'URL.active_url' => '有効なURLではないか、URLの文字数が255文字を超えています',
            'URL.max' => '有効なURLではないか、URLの文字数が255文字を超えています',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (
            $validator->errors()->first('name') == "英数字、ひらがな、カタカナ、漢字で入力してください"
            or $validator->errors()->first('prefectural') == "英数字、ひらがな、カタカナ、漢字で入力してください"
            or $validator->errors()->first('address') == "英数字、ひらがな、カタカナ、漢字で入力してください"
        ) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0012', '01');
            $message = Message::get_message_handle('mhcmer0012', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            // リダイレクト先
            throw new HttpResponseException(
                back()->withInput($this->input)->withErrors($validator)
            );
        } elseif (
            $validator->errors()->first('postcode') == "郵便番号はハイフン不要、7桁で入力してください"
        ) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0014', '01');
            $message = Message::get_message_handle('mhcmer0014', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            // リダイレクト先
            throw new HttpResponseException(
                back()->withInput($this->input)->withErrors($validator)
            );
        } elseif (
            $validator->errors()->first('URL') == "有効なURLではないか、URLの文字数が255文字を超えています"
        ) {
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
