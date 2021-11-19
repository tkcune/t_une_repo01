<?php

namespace App\Librarys\php;

use Illuminate\Support\Arr;

//機能コードクラス
class FunctionCode{
    //機能の文字から機能コードを返す
    //$param string $function 機能
    //@return string 機能コード
    public static function get_functioncode($function){
        //@var array 機能コードのリスト
        $function_list = array(
            'その他' => '00',
            'ログイン' => 'li',
            'パスワード変更' => 'pc',
            '人員' => 'ji',
            '部署' => 'bs',
            '作業場所' => 'sb',
            '作業定義' => 'st',
            '作業指示' => 'ss',
            '作業入力' => 'si',
            '資料' => 'sr',
            '掲示板' => 'kb',
            '権限' => 'kg',
            '付帯定義' => 'ft',
            'マイツリー' => 'mt',
            'システム設定' => 'ss',
            'ファイル取込' => 'fi',
            'ファイル出力' => 'fo',
            'ログ一覧' => 'lg',
            'ネットワーク設定' => 'nw',
            '休止期間' => 'kj',
            '作業進捗' => 'sc',
            '出来高一覧' => 'dd',
            '作業フロー' => 'sf',
            '予定表示-年' => 'yy',
            '予定表示-月' => 'ym',
            '予定表示-週' => 'yw',
            '予定表示-日' => 'yd',
            'ユーザー情報' => 'ur',
            '統合システム管理' => 'ts',
            '顧客' => 'kk',
            'システム休止' => 'qc',
            '共通機能' => 'cm'
        );

        //機能ではなく機能コードが引数であれば、機能コードを返す。
        foreach($function_list as $key => $value){
            //機能コードと一致すれば、
            if($value == $function){
                return $function;
            }
        }

        //@var string 機能コード一致しなければ、00
        $function_code = Arr::get($function_list, $function, '00');
        return $function_code;
    }
}

?>