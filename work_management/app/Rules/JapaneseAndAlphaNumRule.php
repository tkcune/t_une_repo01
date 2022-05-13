<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class JapaneseAndAlphaNumRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^[ぁ-んァ-ヶーa-zA-Z0-9一-龠０-９-]+$/u', $value); //住所の「-」が通るように「-」を追記
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '英数字、ひらがな、カタカナ、漢字で入力してください';
    }
}
