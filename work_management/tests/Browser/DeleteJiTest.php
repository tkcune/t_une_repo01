<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\TestDBDusk;
use Facebook\WebDriver\WebDriverBy;

class DeleteJiTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    //佐藤三郎(ji00000003)の削除
    //データベースのデータを確認し、
    //メッセージ領域に削除しましたという文字を確認する
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            //初期画面に移動
            $browser->visit('/');
            //佐藤三郎に移動する
            $browser->press('#ji-table > tbody > tr:nth-child(3) > td:nth-child(2) > a');
            //佐藤三郎の詳細画面をスクリーンショット
            $browser->screenshot('deleteji/visit_ji00000003');
            //削除の機能の有効化
            $browser->check('#parent > div:nth-child(5) > div > div > input[type=checkbox]:nth-child(9)');
            //削除の有効化をスクリーンショット
            $browser->screenshot('deleteji/enable_delete_ji');
            //削除のクリックする
            $browser->press('#delete');
            //削除の完了をスクリーンショット
            $browser->screenshot('deleteji/complete_delete_ji');
            
            //@var array 結果を確認するデータ
            $data = TestDBDusk::has_miss_delete_ji();
            //データベースのデータが存在しない事を確認する
            foreach($data as $key => $value){
                foreach($value as $row){
                    $this->assertDatabaseMissing($key, $row);
                }
            }
            //メッセージ領域を確認する。セクレターの指定が難しいので、XPathで指定する
            $this->assertEquals($browser->driver->findElement(WebDriverBy::xpath("/html/body/div[1]/header/div/div[2]/p"))->getText(), '削除しました');
        });
    }
}
