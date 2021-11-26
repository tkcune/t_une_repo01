<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\TestDBDusk;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\Test;

class UpdataBsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    //部署Aの情報を変更する
    //部署Aの名前を部署Zに変更、状態を14にする
    //データベースの結果を確認し、メッセージ領域が
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            //初期画面をスクリーンショット
            $browser->screenshot('updatebs/visit_index');
            //部署Aの名前を部署Ｚに変更する
            $browser->value('#palent > input[type=text]', '部署Z');
            //部署Aの状態を14に変更する
            $browser->select('#parent > div:nth-child(3) > div > p > select:nth-child(1)', 14);
            //変更した状態をスクリーンショット(ツリー画面の部署Ａのまま)
            $browser->screenshot('updatebs/update_bs');
            //確定をクリックする
            $browser->press('#parent > div:nth-child(4) > div > div > input[type=submit]:nth-child(1)');
            //変更した状態をスクリーンショットにする(ツリー画面の部署Ａが部署Ｚに変更)
            $browser->screenshot('updatebs/complete_update_bs');

            //@var array 結果を確認するデータ
            $data = TestDBDusk::has_update_bs();
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
