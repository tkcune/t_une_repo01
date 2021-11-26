# sagyokanri

本プログラムの動作のためには<BR>
エンジニア系利用者向けマニュアル - ダウンロードしたLaravelプログラム資源を開発可能にする手順<BR>
を実行後、以下を行う
<P>
データベース構築<BR>
１・データベースを作成<BR>
    
　1-1.PHPMyAdminにアクセスする。<BR>
    
　1-2.画面左の新規作成をクリックする。<BR>
    
　1-3.データベース名をwork_managementと入力し作成をクリック<BR>
    
２・.envファイルの作成<BR>
    
　2-1.env.exampleのファイル名を.envに変更<BR>
    
　2-2.DB_DATABASE=laravelをDB_DATABASE=work_managementに変更<BR>
    
３・.vendorファイルの作成<BR>
    
  3-1.コマンドプロンプトを開きcdコマンドでwork_managementのpublicディレクトリに移動<BR>
    
  3-2.composer installコマンドを実行<BR>
    
４・データの挿入<BR>
    
　4-1.PHPMyAdminにアクセスし、作成したwork_managementデータベースをクリック<BR>
    
　4-2.インポートをクリック<BR>
    
　4-3.アップロードファイル:ファイル選択をクリックし、04.内部設計の04.データベーステストデータにあるzipファイルを選択<BR>
    
　4-4.右下にある実行ボタンをクリック
    
４－２・データの挿入(別の方法)<BR>
    
  4-2-1.コンソールがwork_manegement配下の時に、 php artisan migrateでデータベースのテーブルを作成する<BR>
    
　4-2-2.続けて、 php artisan db:seedで、データベースにデータを挿入する<BR>
    
５・APP_KEYの生成
    
  5-1. php artisan key:generateの実行で、.envファイルのAPP_KEYが生成される<BR>


データベースのテーブルの初期化<BR>
    
1. php artisan migrate:freshで、データベースのテーブルのデータを消去する<BR>
    
2. php artisan db:seedで、データベースのテーブルにデータを挿入する
