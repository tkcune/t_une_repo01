データベース設営手順

1.XAMPP Control Panelを開いて
　ApacgeとMySQLをクリックする
　（名称が緑色になればOK）
2.http://localhost/phpmyadmin/
  にアクセスし新規作成をクリック
3.データベース名入力欄に
　work_managementと記入し作成ボタンを押す
4.コマンドプロンプトを開き
　cd C:\work\sagyokanri\work_management
　と入力し、該当ファイルに移動する。
5.php artisan migrateと入力する。
6.http://localhost/phpmyadmin/にアクセスし
　該当テーブルが存在しているかどうか確認