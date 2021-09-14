<?php

namespace App\Librarys\php;

use Illuminate\Support\Arr;

//メッセージクラス
Class Message{

    //メッセージ番号からメッセージを取得する
    //@param string $message_code メッセージ番号
    //@param array $message_string メッセージに挿入する文字
    //@return array メッセージ、種別、機能
    public static function get_message($message_code, $message_string){

        //@var array 文字を挿入しないメッセージ
        $message_list = array(
            'mhcmer0001' => 'エラーです。エラーID【%s】',
            'mhcmer0002' => '数値項目に文字が入力されました。',
            'mhcmer0003' => '入力されてない項目があります。',
            'mhcmer0004' => 'メール送信試験に失敗しました',
            'mhcmer0005' => '作業者に作業場所への参照権限がありません',
            'mhcmer0006' => '作業者に掲示板への参照権限がありません',
            'mhcmer0007' => '作業者のメールアドレスがありません',
            'mhcmer0008' => '運用終了月が運用開始月より過去になっています',
            'mscmer0001' => '最大リソース量超過',
            'mhcmnm0001' => '%sが届きました：%s',
            'mhcmwn0001' => '該当する情報がありません。',
            'mhcmwn0002' => '現在、この機能を参照できるのはあなたのみです',
            'mhcmok0001' => '登録しました',
            'mhcmok0002' => '変更しました',
            'mhcmok0003' => '削除しました',
            'mhcmok0004' => '%sをクリップボードに複写しました',
            'mhcmok0005' => 'クリップボードへの複写を取り消しました',
            'mhcmok0006' => '削除ができるようになりました',
            'mhcmok0007' => '削除ができなくなりました',
            'mhcmok0008' => '移動しました',
            'mhcmok0009' => '挿入しました',
            'mhcmok0010' => '投影しました',
            'mhcmok0011' => 'メール送信試験に成功しました',
            'malier0001' => 'ログインできませんでした　ユーザ名またはパスワードが違います　詳細は管理者%sにお問い合わせください',
            'malier0002' => 'ログインパスワードの更新期限を超えました　詳細は管理者%sにお問い合わせください',
            'mhliwn0001' => 'ログインパスワードの更新期限まであと%s日です',
            'mapcer0001' => '新しいパスワードが不正です。',
            'mhbswn0001' => '部署状態が稼働中であるにも関わず、現在の日付が運用開始終了の範囲外です',
            'mhbswn0002' => '部署状態が廃止であるにも関わず、現在の日付が運用開始終了の範囲内です',
            'mhbswn0003' => '今、この部署には何の権限がありません',
            'mhjier0001' => 'メールアドレスが重複しています',
            'mhjier0002' => 'システム管理者は一人以上必要です',
            'mhjiwn0001' => '人員状態が在職であるにも関わず、現在の日付が運用開始終了の範囲外です',
            'mhjiwn0002' => '人員状態が退職であるにも関わず、現在の日付が運用開始終了の範囲内です',
            'mhstwn0001' => '付帯定義に作業内容連絡タイミングが存在します',
            'mhster0001' => '作業内容連絡タイミングを指定している場合、連絡先となる作業者は入力必須になります',
            'mhster0002' => '作業報告が不要であるにも関わず、アクションありになっています',
            'mhster0003' => '作業指示者に作業場所への参照権限がありません',
            'mhster0004' => '作業指示者に掲示板への参照権限がありません',
            'mhfook0001' => 'ダウンロードファイル作成中',
            'msfier0001' => 'ファイルに異常があります',
            'msfier0002' => 'ファイル書式異常',
            'msfier0003' => '設定値無し(line =>%s col =>%s)',
            'msfier0004' => '形式異常(line =>%s col =>%s)',
            'msfier0005' => '設定値異常(line =>%s col =>%s)',
            'msfier0006' => '該当部署無し(line =>%s col =>%s)',
            'msfier0007' => '該当人員無し(line =>%s col =>%s)',
            'msfier0008' => '該当作業場所無し(line =>%s col =>%s)',
            'msfier0008' => '該当作業定義無し(line =>%s col =>%s)',
            'msfier0010' => '該当作業指示無し(line =>%s col =>%s)',
            'msfier0011' => '該当作業進捗無し(line =>%s col =>%s)',
            'msfier0012' => '該当掲示板無し(line =>%s col =>%s)',
            'msfiwn0001' => '文字長超過(line =>%s col =>%s)',
            'mhfiwn0001' => 'ファイルに警告があります',
            'mhkker0001' => '運用中の顧客は削除できません',
            'mppuwn0001' => '%sを実行しますか。',
            'mhkgwn0001' => '既に権限は付与されています',
            'mhqcnm0001' => 'システム休止中です再開予定日時は%sです',
            'mhqcnm0002' => 'URL指定イベント%sによる画面表示です'
        );
        //mb_substr_count
        //@var int 引数の挿入するメッセージの数
        $message_string_count = count($message_string);

        //@var string メッセージリストからメッセージを取得する
        $message = Arr::get($message_list, $message_code, '');

        //メッセージ番号が一致しない場合
        if($message == ''){
            
            return ['message_code_error', 'er', '00'];
        
        }else{

            //@var int %sの数を数える
            $count_percent_s = mb_substr_count($message, '%s');
            //メッセージに挿入する文字がひとつの場合
            if($count_percent_s == 1){
                //引数の挿入する文字の数がひとつの場合
                if($message_string_count == 1){
                    $message = sprintf($message, $message_string[0]);
                }else{
                    //引数の挿入する文字の数が合わない場合
                    return [$message, 'er', '00'];
                }
            }else if($count_percent_s == 2){
                //メッセージに挿入する文字がふたつの場合
                if($message_string_count == 2){
                    //引数の挿入する文字の数がひとつの場合
                    $message = sprintf($message, $message_string[0], $message_string[1]);
                }else{
                    //引数の挿入する文字の数が合わない場合
                    return [$message, 'er', '00'];
                }
            }
            //メッセージ番号が一致する場合
            //@var string メッセージ番号から種別を取得する
            $type = mb_substr($message_code, 4, 2);
            //@var string メッセージ番号から機能を取得する
            $function = mb_substr($message_code, 2, 2);

            return [$message, $type, $function];
        }
    }
}

?>