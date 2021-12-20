<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class BsUpdateErrorTest extends TestCase
{
    //csrfトークンを無効にする
    use WithoutMiddleware;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //部署のデータをなるべく不正に変更するテスト
    public function test_example()
    {
        //bladeを表示する為に、セッションを設定する
        session(['client_id' => 'aa00000001']);

        //--不正なリクエスト
        //responsible_person_idとstatusの値が不正
        //@var array リクエストの配列
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => 0xffff."\n dfsjkadaklsd", 'management_number' => 'ji00000001',
        'responsible_person_id' => 'ji$%FD',
        'start_day' => now(), 'finish_day' => '2021-09-06 10:21:02','status' => '(', 'remarks' => "dfasjkd \n jfsdkl"];
        
        //@var Response 更新のレスポンス
        $response = $this->patch('/psbs01/update', $bs_update_data);
        
        //リダイレクトを確認する
        $response->assertStatus(302);
    }
}
