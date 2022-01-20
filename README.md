# sagyokanri

本プログラムの動作のためには<BR>
エンジニア系利用者向けマニュアル - ダウンロードしたLaravelプログラム資源を開発可能にする手順<BR>
を実行後、以下を行う
<P>
データベース構築<BR>
  
１ データベースを作成<BR>
  
　1-1 PHPMyAdminにアクセスする。<BR>
  
　1-2 画面左の新規作成をクリックする。<BR>
  <br>
　1-3 データベース名をwork_managementと入力し作成をクリック<BR>
  <br>
  <br>
２ .envファイルの作成<BR>
  <br>
　2-1 .env.exampleのファイル名を.envに変更<BR>
  <br>
　2-2 DB_DATABASE=laravelをDB_DATABASE=work_managementに変更<BR>
  <br>
  <br>
３ .vendorファイルの作成<BR>
  <br>
  3-1 コマンドプロンプトを開きcdコマンドでwork_managementのpublicディレクトリに移動<BR>
  <br>
  3-2 composer installコマンドを実行<BR>
  <br>
  <br>
４・データの挿入<BR>
    
　4-1.コンソールがwork_manegement配下の時に、 php artisan migrateでデータベースのテーブルを作成する<BR>
    
　4-2.続けて、 php artisan db:seedで、データベースにデータを挿入する<BR>
    
５・APP_KEYの生成
    
　5-1. php artisan key:generateの実行で、.envファイルのAPP_KEYが生成される<BR>
  
データベースのテーブルの初期化<BR>
    
1. php artisan migrate:freshで、データベースのテーブルのデータを消去する<BR>
    
2. php artisan db:seedで、データベースのテーブルにデータを挿入する<BR>

最新のデータベースに対応したseederの作成方法<BR>

1. コマンドプロンプトのwork_manegement配下でcomposer require --dev "orangehill/iseed"を実行(既に実行済みの場合は省略)<BR>

2. コマンドプロンプトのwork_manegement配下でphp artisan iseed {table_name}を実行({table_name}は対応したテーブル名を記載)<BR>
