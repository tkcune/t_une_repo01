<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\TestDBDusk;
use Facebook\WebDriver\WebDriverBy;

class MoveBsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    //部署Fを部署Gに移動する
    //データベースの結果の確認と
    //メッセージ領域のクリックボードにコピーしましたと移動しましたの文字の確認。
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            //初期画面に移動
            $browser->visit('/');
            //部署Fに移動する
            $browser->click('#bs-table > tbody > tr:nth-child(5) > td:nth-child(2) > a');
            //部署Fに移動をスクリーンショット
            $browser->screenshot('movebs/visit_bs00000006');
            //複写をする
            $browser->press('#copyTarget');
            //複写をスクリーンショット
            $browser->screenshot('movebs/clipboard_bs');
            //部署Cに移動する
            $browser->click('#parent > div:nth-child(1) > div:nth-child(3) > p > a');
            //部署Gに移動する
            $browser->click('#bs-table > tbody > tr:nth-child(3) > td:nth-child(2) > a');
            //部署Gの詳細画面をスクリーンショット
            $browser->screenshot('movebs/visit_bs00000007');
            //部署Fを部署Gに移動する
            $browser->press('#list > div.department-area > div:nth-child(1) > div > div > form:nth-child(2) > button');
            //移動をスクリーンショットする
            $browser->screenshot('movebs/complete_movebs');
;            //@var array 存在する事を確認するデータ
            $has_data = TestDBDusk::has_move_bs();
            foreach($has_data as $key => $value){
                foreach($value as $row){
                    $this->assertDatabaseHas($key, $row);
                }
            }
            //@var array 存在しない事を確認するデータ
            $has_miss_data = TestDBDusk::has_miss_move_bs();
            //データベースのデータが存在しない事を確認する
            foreach($has_miss_data as $key => $value){
                foreach($value as $row){
                    $this->assertDatabaseMissing($key, $row);
                }
            }
            //メッセージ領域を確認する。セクレターの指定が難しいので、XPathで指定する
            $this->assertEquals($browser->driver->findElement(WebDriverBy::xpath("/html/body/div[1]/header/div/div[2]/p"))->getText(), '移動しました');
        });
    }
}
