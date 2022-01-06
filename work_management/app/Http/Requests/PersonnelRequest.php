<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AlphaNumHalf;
use App\Facades\OutputLog;
use App\Libraries\php\Message;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\JapaneseAndAlphaNumRule;

class PersonnelRequest extends FormRequest
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
            'name' =>['required',new JapaneseAndAlphaNumRule],
            'management_number'=>'required',
            'status'=>['required',new StatusRule],
            //'password' => new AlphaNumHalf,
        ];
    }

    public function messages()
    {
        //OutputLog::message_log(__FUNCTION__, 'mhcmer0003','01');

        return [
            'name.required'=>'名前を入力してください',
            'management_number.required'=>'管理者番号を入力してください',
            'status.required'=>'状態を入力してください',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        
        if($validator->errors()->first('password')=="パスワードは半角英数字で入力してください"){
            $message = $validator->errors()->first('password');
            session(['message'=>$message]);
            // リダイレクト先
            throw new HttpResponseException(
            back()->withInput($this->input)->withErrors($validator)
            );
        }elseif($validator->errors()->first('name')=="英数字、ひらがな、カタカナ、漢字で入力してください"){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0012','01');
            $message = Message::get_message('mhcmer0012',[0=>'']);
            session(['message'=>$message[0]]);
            // リダイレクト先
            throw new HttpResponseException(
            back()->withInput($this->input)->withErrors($validator)
            );
        }elseif($validator->errors()->first('status')=="不正な入力が行われました"){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0013','01');
            $message = Message::get_message('mhcmer0013',[0=>'']);
            session(['message'=>$message[0]]);
            dd($message);
            // リダイレクト先
            throw new HttpResponseException(
            back()->withInput($this->input)->withErrors($validator)
            );
        }else{
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

  
}
