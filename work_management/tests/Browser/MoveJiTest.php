<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\TestDBDusk;
use Facebook\WebDriver\WebDriverBy;

class MoveJiTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    //佐々木花子を部署Bから部署Cに移動する
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            //初期画面に移動する
            $browser->visit('/');
            //佐々木花子(ji00000004)の詳細画面に移動する
            $browser->click('#ji-table > tbody > tr:nth-child(4) > td:nth-child(2) > a');
            //佐々木花子の詳細画面をスクリーンショット
            $browser->screenshot('moveji/visit_ji00000004');
            //複写する
            $browser->press('#copyTarget');
            //佐々木花子の複写をスクリーンショット
            $browser->screenshot('moveji/copy_clipboard_ji');
            //部署Bに移動する
            $browser->click('#parent > div:nth-child(1) > div:nth-child(3) > a');
            //部署Aに移動する
            $browser->click('#parent > div:nth-child(1) > div:nth-child(3) > p > a');
            //部署Cに移動する
            $browser->click('#bs-table > tbody > tr:nth-child(2) > td:nth-child(2) > a');
            //部署Cの詳細画面をスクリーンショット
            $browser->screenshot('moveji/visit_bs00000003');
            //移動をクリックする
            $browser->press('#list > div:nth-child(2) > div > div > form:nth-child(2) > button');
            //佐々木花子の部署Cの移動をスクリーンショット
            $browser->screenshot('moveji/complete_move_ji');
            //@var array 存在する事を確認するデータ
            $has_data = TestDBDusk::has_move_ji();
            foreach($has_data as $key => $value){
                foreach($value as $row){
                    $this->assertDatabaseHas($key, $row);
                }
            }
            //@var array 存在しない事を確認するデータ
            $has_miss_data = TestDBDusk::has_miss_move_ji();
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
