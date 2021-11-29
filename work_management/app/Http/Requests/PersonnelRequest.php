<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AlphaNumHalf;

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
            'client_id' => '',
            'personnel_id' =>'',
            'name' =>'',
            'email' =>'',
            'password' => [new AlphaNumHalf],
            'management_number'=>'',
            'status'=>'',
        ];
    }

    public function messages()
    {
        return [
            
        ];
    }
}
