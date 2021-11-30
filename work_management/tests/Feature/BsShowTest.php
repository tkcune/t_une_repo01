<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BsShowTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //部署Bに移動する
    public function test_example()
    {
        //セッションをセットしておかないと、bladeの当たりでエラーになる。
        session(['client_id' => "aa00000001"]);
        //@var Response 部署Bにアクセスする
        $response = $this->get('/show/aa00000001/bs00000002');

        //目的のbladeが返る事を確認する
        $response->assertViewIs('pacm01.pacm01');
        //レスポンスのhttpコードがちゃんとしている事を確認する
        $response->assertStatus(200);
    }
}
