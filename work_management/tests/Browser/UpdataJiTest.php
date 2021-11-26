<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\TestDBDusk;
use Facebook\WebDriver\WebDriverBy;

class UpdataJiTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    //田中次郎(ji00000002)の情報を変更する
    //田中次郎の名前を田中五郎に、状態を13に変更する
    //結果をデータベースで確認し、メッセージ領域を変更しましたを確認する
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            //田中次郎の詳細画面に移動する
            $browser->press('#ji-table > tbody > tr:nth-child(2) > td:nth-child(2) > a');
            //田中次郎の詳細画面をスクリーンショット
            $browser->screenshot('updateji/visit_jji00000002');
            //田中次郎の名前を田中五郎に変更する
            $browser->value('#palent > input[type=text]', '田中五郎');
            //部署Aの状態を14に変更する
            $browser->select('#parent > div:nth-child(4) > div > p > select', 13);
            //変更した状態をスクリーンショット(ツリー画面が田中次郎のまま)
            $browser->screenshot('updateji/update_ji');
            //パスワードを入力する(パスワードの検証は行わない)
            $browser->value('#password', '1q1q1q1q1q');
            //確定をクリックする
            $browser->press('#parent > div:nth-child(5) > div > div > input[type=submit]:nth-child(1)');
            //変更した状態をスクリーンショットにする(ツリー画面の田中次郎が田中五郎に変更)
            $browser->screenshot('updateji/complete_update_ji');

            //@var array 結果を確認するデータ
            $data = TestDBDusk::has_update_ji();
            //データベースを確認する
            foreach($data as $key => $value){
                foreach($value as $row){
                    $this->assertDatabaseHas($key, $row);
                }
            }
            //メッセージ領域を確認する。セクレターの指定が難しいので、XPathで指定する
            $this->assertEquals($browser->driver->findElement(WebDriverBy::xpath("/html/body/div[1]/header/div/div[2]/p"))->getText(), '変更しました');
        });
    }
}
