<?php

namespace App\Http\Controllers;

use SimpleXMLElement;

/**
 * ポップアップ機能を実現するコントローラー
 */
class Pppu01Controller extends Controller
{
    /**
     * xml情報取得（現在はダミープログラム　ログイン機能実現後に中身を実装）
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO:セッション情報からログインしているユーザIDを取得
        // TODO:ユーザIDから通知情報の一覧を取得
        // TODO:通知情報の一覧をxml形式に変換
$xml_text = <<< XML
<wxml>
  <head>
    <name>作業管理システム</name>
    <ver>1.01</ver>
    <sendtime>2022/3/12 10:11:13</sendtime>
    <maxid>I000000099</maxid>
  </head>
  <body>
  <info>
      <id>I000000098</id>
      <title>機関室管理</title>
      <stepno>S000000001</stepno>
      <from>
        <id>A000000001</id>
        <name>山田　一郎</name>
        <mail>aaa@aaa.or.jp</mail>
      </from>
      <type>
        <code>0001</code>
        <name>開始指示</name>
      </type>
      <wtime>
        <code>0001</code>
        <name>定刻開始</name>
        <id></id>
        <time>2022/3/14 12:00:00</time>
      </wtime>
      <work>
        <id>W000000001</id>
        <name>第二機関室エンジン清掃作業</name>
     </work>
    </info>
    <info>
      <id>I000000099</id>
      <title>操舵室管理</title>
      <stepno>S000000002</stepno>
      <from>
        <id>A000000002</id>
        <name>吉田　次郎</name>
        <mail>bbb@bbb.or.jp</mail>
      </from>
      <type>
        <code>0001</code>
        <name>開始指示</name>
      </type>
      <wtime>
        <code>0002</code>
        <name>前作業完了後</name>
        <id>S000000001</id>
        <time></time>
      </wtime>
      <work>
        <id>W000000002</id>
        <name>操舵室清掃作業</name>
     </work>
    </info>
  </body>
</wxml>
XML;
        // xml形式の情報を表示（PCやスマフォのアプリに返却）
        return response($xml_text, 200, ['Content-Type'=> 'application/xml']);
     }
}
