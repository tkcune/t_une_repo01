<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DclgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //@var string dclgのカラム列
        $dclg_column = 'client_id, log_id, created_at, updated_at, type, user, function, program_pass, log';
        //@var string dclgのホルダー
        $dclg_holder = '?, ?, ?, ?, ?, ?, ?, ?, ?';
        //@var array dclgに挿入するデータ
        $dclg_insert_data_list = [
            ['kk00000000', 1, '2021-12-01 03:01:36', '2021-12-01 03:01:36', 'wn', 'yamada@itirou.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 2, '2021-12-01 03:03:59', '2021-12-01 03:03:59', 'si', 'sasaki@kyouko.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 3, '2021-12-01 03:04:03', '2021-12-01 03:04:03', 'wn', 'yamada@itirou.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 4, '2021-12-01 03:04:06', '2021-12-01 03:04:06', 'si', 'sasaki@kyouko.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 5, '2021-12-01 03:09:28', '2021-12-01 03:09:28', 'nm', 'sasaki@kyouko.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 6, '2021-12-01 03:09:32', '2021-12-01 03:09:32', 'nm', 'takahasi@hanako.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 7, '2021-12-01 03:09:35', '2021-12-01 03:09:35', 'nm', 'takahasi@hanako.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 8, '2021-12-09 03:38:54', '2021-12-09 03:38:54', 'wn', 'satou@saburou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 9, '2021-12-09 03:39:04', '2021-12-09 03:39:04', 'wn', 'ueda@sirou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 10, '2021-12-09 03:39:06', '2021-12-09 03:39:06', 'sy', 'ueda@sirou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 11, '2021-12-09 03:40:54', '2021-12-09 03:40:54', 'wn', 'satou@saburou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 12, '2021-12-09 03:41:15', '2021-12-09 03:41:15', 'ok', 'tanaka@zirou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 13, '2021-12-10 00:59:28', '2021-12-10 00:59:28', 'ok', 'yamada@itirou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 14, '2021-12-10 00:59:46', '2021-12-10 00:59:46', 'er', 'tanaka@zirou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 15, '2021-12-10 00:59:49', '2021-12-10 00:59:49', 'er', 'yamada@itirou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 16, '2021-12-10 01:18:16', '2021-12-10 01:18:16', 'nm', 'august@roma.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 17, '2021-12-10 01:18:53', '2021-12-10 01:18:53', 'er', 'august@roma.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 18, '2021-12-10 01:19:30', '2021-12-10 01:19:30', 'si', 'simoyama@gorou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 19, '2021-12-10 01:19:56', '2021-12-10 01:19:56', 'si', 'august@roma.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 20, '2021-12-10 01:35:47', '2021-12-10 01:35:47', 'si', 'simoyama@gorou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 21, '2021-12-10 02:59:05', '2021-12-10 02:59:05', 'sy', 'sasaki@hanako.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 22, '2021-12-10 02:59:13', '2021-12-10 02:59:13', 'er', 'august@roma.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 23, '2021-12-10 02:59:22', '2021-12-10 02:59:22', 'si', 'sasaki@hanako.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始'],
            ['kk00000000', 24, '2021-12-10 02:59:25', '2021-12-10 02:59:25', 'er', 'tanaka@zirou.com', 'cm', 'C:\\work\\repo\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']
        ];
        foreach($dclg_insert_data_list as $insert_data){
            DB::insert('insert into dclg01 ('. $dclg_column .') VALUE ('. $dclg_holder .')', $insert_data);
        }
    }
}
