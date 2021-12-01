<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Libraries\php\Message;
use Illuminate\Support\Facades\DB;

class BsUpdateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //部署の更新のテスト
    //成功と失敗のテスト
    //成功の場合
    //status = 13, 18の場合
    //operation_start_dateとoperation_end_dateも変更になる。
    //statusが13,18の場合、nameとstatusが変更になる。
    //失敗の場合
    //requestのname,manegement_number,statusが空文字
    //リダイレクトすると同時に、エラーメッセージが出る
    //manegement_numberがdcji01上にない場合
    //indexにリダイレクトする
    public function test_example()
    {
        //bladeを表示する為に、セッションを設定する
        session(['client_id' => 'aa00000001']);

        //--成功のリクエスト
        //@var array リクエストの配列
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署X', 'management_number' => 'ji00000001', 'responsible_person_id' => 'ji00000001', 'status' => '11'];
         //@var Response 更新のレスポンス
        $response = $this->patch('/', $bs_update_data);
        //データが更新されたか、確認する。
        $this->assertDatabaseHas('dcbs01', ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署X', 'responsible_person_id' => 'ji00000001', 'status' => '11']);
        //リダイレクトを確認する
        $response->assertStatus(302);
        //--成功のリクエスト
        print("end seccess request complete update \n");

        //--成功のリクエスト(status = 13)
        //@var array リクエストの配列
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署X', 'management_number' => 'ji00000001', 'responsible_person_id' => 'ji00000001', 'status' => '13'];
         //@var Response 更新のレスポンス
        $response = $this->patch('/', $bs_update_data);
        //データが更新されたか、確認する。
        $this->assertDatabaseHas('dcbs01', ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署X', 'responsible_person_id' => 'ji00000001', 'status' => '13']);
        //運用開始日がnullでない事を確認する
        $this->assertNotEmpty(DB::select('select operation_start_date from dcbs01 where department_id = ?', ['bs00000001'])[0]->operation_start_date);
        //リダイレクトを確認する
        $response->assertStatus(302);
        //--成功のリクエスト
        print("変更わかりやすくする為に現在時刻 ".now()."\n");
        print("operation_start_date ".DB::select('select operation_start_date from dcbs01 where department_id = ?', ['bs00000001'])[0]->operation_start_date."\n");
        print("end request status is 13 \n");

        //--成功のリクエスト(status = 18)
        //@var array リクエストの配列
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署X', 'management_number' => 'ji00000001', 'responsible_person_id' => 'ji00000001', 'status' => '18'];
         //@var Response 更新のレスポンス
        $response = $this->patch('/', $bs_update_data);
        //データが更新されたか、確認する。
        $this->assertDatabaseHas('dcbs01', ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署X', 'responsible_person_id' => 'ji00000001', 'status' => '18']);
        //運用開始日がnullでない事を確認する
        $this->assertNotEmpty(DB::select('select operation_end_date from dcbs01 where department_id = ?', ['bs00000001'])[0]->operation_end_date);
        //リダイレクトを確認する
        $response->assertStatus(302);
        //--成功のリクエスト
        print("変更わかりやすくする為に現在時刻 ".now()."\n");
        print("operation_end_date ".DB::select('select operation_end_date from dcbs01 where department_id = ?', ['bs00000001'])[0]->operation_end_date."\n");
        print("end request status is 18 \n");


        //--失敗のリクエスト(nameが空文字)
        //@var array 失敗のリクエストの配列(nameが空文字)
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '', 'management_number' => 'ji00000001', 'responsible_person_id' => 'ji00000001', 'status' => '11'];
        $response = $this->patch('/', $bs_update_data);
        //セッションのメッセージを確認する
        $this->assertEquals(session()->get('message'), Message::get_message('mhcmer0003', [])[0]);
        //リダイレクトを確認する
        $response->assertStatus(302);
        //--失敗のリクエスト(nameが空文字)
        print("end fail request name empty \n");

        //--失敗のリクエスト(management_numberが空文字)
        //@var array 失敗のリクエストの配列(management_numberが空文字)
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署A', 'management_number' => '', 'responsible_person_id' => 'ji00000001', 'status' => '11'];
        $response = $this->patch('/', $bs_update_data);
        //セッションのメッセージを確認する
        $this->assertEquals(session()->get('message'), Message::get_message('mhcmer0003', [])[0]);
        //リダイレクトを確認する
        $response->assertStatus(302);
        //--失敗のリクエスト(management_numberが空文字)
        print("end fail request management_number empty \n");

        //--失敗のリクエスト(statusが空文字)
        //@var array 失敗のリクエストの配列(management_numberが空文字)
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署A', 'management_number' => '', 'responsible_person_id' => 'ji00000001', 'status' => ''];
        $response = $this->patch('/', $bs_update_data);
        //セッションのメッセージを確認する
        $this->assertEquals(session()->get('message'), Message::get_message('mhcmer0003', [])[0]);
        //リダイレクトを確認する
        $response->assertStatus(302);
        //--失敗のリクエスト(statusが空文字)
        print("end fail request status empty \n");

        //--失敗のリクエスト(manegement_number = ji000000012がdcji01上に存在しない)
        //@var array 失敗のリクエストの配列
        $bs_update_data = ['client_id' => 'aa00000001', 'department_id' => 'bs00000001',
        'name' => '部署A', 'management_number' => 'ji00000012', 'responsible_person_id' => 'ji00000001', 'status' => '11'];
        $response = $this->patch('/', $bs_update_data);
        //indexのリダイレクトを確認する
        $response->assertRedirect('/');
        //--失敗のリクエスト(manegement_number = ji000000012がdcji01上に存在しない)
        print("end fail request ji00000012 is not \n");
    }
}
