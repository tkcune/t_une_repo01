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
　2-2.APP_KEY=をAPP_KEY=base64:Nz0Pe/vy8ScPBejgsFqhUijUCJ2gSFLLfDUEpOdLNx8=に変更<BR>
　2-3.DB_DATABASE=laravelをDB_DATABASE=work_managementに変更<BR>
３・.vendorファイルの作成<BR>
  3-1.コマンドプロンプトを開きcdコマンドでwork_managementのpublicディレクトリに移動<BR>
  3-2.composer updateコマンドを実行<BR>
４・テーブルを作成<BR>
　4-1.コマンドプロンプトを開きcdコマンドでwork_managementディレクトリに移動<BR>
　4-2.php artisan migrateと入力しテーブルを作成<BR>
