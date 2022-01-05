<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Facades\OutputLog;
use Illuminate\Support\Str;

class OutputLogTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    //ログ出力をテストする
    public function test_example()
    {
        //@var string ランダム文字列を作成する事で、テストのログが被らないようにする
        $log = Str::random(20);
        //ログを出力する
        OutputLog::log(__FUNCTION__, 'er', '00', $log);
        //ログがデータベースに登録されているか、確認する
        $this->assertDatabaseHas('dclg01', ['type' => 'er', 'function' => '00', 'log' => $log]);
    }
}
