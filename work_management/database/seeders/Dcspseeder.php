<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dcspseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dcsp01')->insert([
            'stamp_id' => '01',
            'content' => '了解',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '02',
            'content' => '拒否',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '03',
            'content' => '肯定',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '04',
            'content' => '否定',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '05',
            'content' => '疑問',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '06',
            'content' => '参照',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '07',
            'content' => '歓迎',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '08',
            'content' => '笑い',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '09',
            'content' => '悲しみ',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '10',
            'content' => '怒り',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '11',
            'content' => '良い',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '12',
            'content' => '悪い',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '13',
            'content' => '保留',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '14',
            'content' => '感謝',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
