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
            'base64' =>'base64.yes',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '02',
            'content' => '拒否',
            'base64' => 'base64.no',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '03',
            'content' => '肯定',
            'base64' => 'base64.circle',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '04',
            'content' => '否定',
            'base64' => 'base64.cross',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '05',
            'content' => '疑問',
            'base64' => 'base64.question',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '06',
            'content' => '参照',
            'base64' => 'base64.reference',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '07',
            'content' => '歓迎',
            'base64' => 'base64.celebration',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '08',
            'content' => '笑い',
            'base64' => 'base64.smile',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '09',
            'content' => '悲しみ',
            'base64' => 'base64.sad',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '10',
            'content' => '怒り',
            'base64' => 'base64.angry',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '11',
            'content' => '良い',
            'base64' => 'base64.good',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '12',
            'content' => '悪い',
            'base64' => 'base64.bad',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '13',
            'content' => '保留',
            'base64' => 'base64.onhold',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dcsp01')->insert([
            'stamp_id' => '14',
            'content' => '感謝',
            'base64' => 'base64.thanks',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
