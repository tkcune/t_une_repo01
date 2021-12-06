<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\TestDBDusk;
use Facebook\WebDriver\WebDriverBy;

class DeleteBsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    //部署Dを削除する
    //部署Ｄの配下も削除される事をデータベースで確認し、
    //メッセージ領域に削除しましたを確認する
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            //初期画面に移動
            $browser->visit('/');
            //部署Dに移動する
            $browser->press('#bs-table > tbody > tr:nth-child(3) > td:nth-child(2) > a');
            //部署Dの詳細画面をスクリーンショット
            $browser->screenshot('deletebs/visit_bsD');
            //削除の機能の有効化
            $browser->check('#parent > div:nth-child(4) > div > div > input[type=checkbox]:nth-child(9)');
            //削除の有効化をスクリーンショット
            $browser->screenshot('deletebs/enable_delete_bs');
            //削除のクリックする
            $browser->press('#delete');
            //削除の完了をスクリーンショット
            $browser->screenshot('deletebs/complete_delete_bs');

            //@var array 結果を確認するデータ
            $data = TestDBDusk::has_miss_delete_bs();
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
