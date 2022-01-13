<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Libraries\php\Service\StatusCheck;

class StatusCheckTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    //人事の状態テスト
    public function test_example()
    {
        //@var array 人事の状態のデータの配列
        $status_data = ['10', '11', '12', '13', '14', '18'];
        //@var StatusCheckクラス 状態クラス
        $status_check = new StatusCheck();
        //テストする
        foreach($status_data as $data){
            //@var string 時刻または、null
            [$operation_start_date,$operation_end_date] = $status_check->statusCheck($data);
            //nullであれば、'null'を表示する
            print($operation_start_date ? $operation_start_date.' ': 'null ');
            //nullであれば、'null'を表示する
            print($operation_end_date ? $operation_end_date: 'null'."\n");
            //状態の結果をテストする
            if($data == '10'){
                //nullを確認する
                $this->assertEquals($operation_start_date, null);
                //nullを確認する
                $this->assertEquals($operation_end_date, null);
            }
            if($data == '11'){
                //nullを確認する
                $this->assertEquals($operation_start_date, null);
                //nullを確認する
                $this->assertEquals($operation_end_date, null);
            }
            if($data == '12'){
                //nullを確認する
                $this->assertEquals($operation_start_date, null);
                //nullを確認する
                $this->assertEquals($operation_end_date, null);
            }
            if($data == '13'){
                //nullでない事をテストする
                $this->assertNotEmpty($operation_start_date);
                //nullを確認する
                $this->assertEquals($operation_end_date, null);
            }
            if($data == '14'){
                //nullを確認する
                $this->assertEquals($operation_start_date, null);
                //nullを確認する
                $this->assertEquals($operation_end_date, null);
            }
            if($data == '18'){
                //nullを確認する
                $this->assertEquals($operation_start_date, null);
                //nullでない事をテストする
                $this->assertNotEmpty($operation_end_date);
            }
        }
    }
}
