<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\TestDBDusk;
use Facebook\WebDriver\WebDriverBy;

class CreateBsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    //部署A配下に、部署Tを新規作成する
    //部署Tが作成されている事をデータベースで確認し、
    //メッセージ領域に登録しましたと記載される
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            //indexのルーティングにアクセスする
            $browser->visit('/');
            //初期画面をスクリーン
            $browser->screenshot('createbs/visit_index');

            //部署の新規作成画面に移動
            $browser->press('#parent > div:nth-child(4) > div > div > form:nth-child(2) > input[type=submit]:nth-child(2)');
            //新規作成画面のスクリーンショット
            $browser->screenshot('createbs/visit_create_bs');
            
            //新規作成画面
            //部署名に部署Tを代入する
            $browser->value('#palent > input[type=text]', '部署T');
            //状態を11にセットする
            $browser->select('#parent > div:nth-child(2) > div > p > select', 11);
            //新規作成画面において、値をセットした状態をスクリーンショット
            $browser->screenshot('createbs/set_create_bs');
            //確定のボタンをクリックする
            $browser->press('#parent > div:nth-child(3) > div > div > input[type=submit]');

            //部署Bに移動し、部署Tが作成されている事をスクリーンショット
            $browser->press('#bs-table > tbody > tr:nth-child(1) > td:nth-child(2) > a');
            $browser->screenshot('createbs/complete_new_create_bs');

            //@var array テスト結果確認のデータ
            $data = TestDBDusk::has_create_bs();
            //データベースを確認する
            foreach($data as $key => $value){
                foreach($value as $row){
                    $this->assertDatabaseHas($key, $row);
                }
            }
            //メッセージ領域を確認する。セクレターの指定が難しいので、XPathで指定する
            $this->assertEquals($browser->driver->findElement(WebDriverBy::xpath("/html/body/div[1]/header/div/div[2]/p"))->getText(), '登録しました');
        });
    }
}
