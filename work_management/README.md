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
    
  4-1.コンソールがwork_manegement配下の時に、 php artisan migrateでデータベースのテーブルを作成する<BR>
    
　4-2.続けて、 php artisan db:seedで、データベースにデータを挿入する<BR>
    
５・APP_KEYの生成
    
  5-1. php artisan key:generateの実行で、.envファイルのAPP_KEYが生成される<BR>


データベースのテーブルの初期化<BR>
    
1. php artisan migrate:freshで、データベースのテーブルのデータを消去する<BR>
    
2. php artisan db:seedで、データベースのテーブルにデータを挿入する

最新のデータベースに対応したseederの作成方法<BR>

1. コマンドプロンプトのwork_manegement配下でcomposer require --dev "orangehill/iseed"を実行(既に実行済みの場合は省略)

2. コマンドプロンプトのwork_manegement配下でphp artisan iseed {table_name}を実行({table_name}は対応したテーブル名を記載)

---
画像をbase64化にする方法(storage/image下の画像をconfig/base64.phpに保存する)

1. base64にしたい画像をstorage/image下に配置する
2. コンソールから,`php artisan command:transbase64'を実行する
3. 以下、注意点
4. storage/image下の画像を全てbase64にして、毎回書き込むので、画像を削除してしまうとconfig/base64.phpに書き込まれない。画像を消してしまうと、元もとあるconfig/base64の行を削除してしまう。
---