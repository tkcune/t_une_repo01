<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DcjiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //@var string dcji01のカラム列
        $dcji_columns = 'client_id, personnel_id, name, email, password, password_update_day, status, management_personnel_id, login_authority, system_management, operation_start_date, operation_end_date, remarks, created_at, updated_at';
        //@var string カラム列のホルダー
        $dcji_holder = '?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
        //@var array dcbs01の挿入データ
        $dcji_insert_data_list = [
            ['aa00000001', 'ji00000001', '山田一郎', 'yamada@itirou.com', '$2y$10$itulf4IwKeCWlfyuAl7IhetjEUV/xTG/MoNJ4d6M1eAckr8dC2dA.', '2021-09-06 10:22:19', '10', 'ji00000001', 1, 1, '2021-09-06 10:24:22', NULL, NULL, '2021-09-06 01:22:19', '2021-09-06 01:22:19'],
            ['aa00000001', 'ji00000002', '田中次郎', 'tanaka@zirou.com', '$2y$10$.jmhtjQiOD5I00Fxe1.uDeAQC9nHS0sZ8Ixjy4pzJvk3yS2Bnwf4K', '2021-09-06 10:22:57', '11', 'ji00000002', 1, 1, '2021-09-06 10:24:22', NULL, NULL, '2021-09-06 01:22:57', '2021-09-06 01:22:57'],
            ['aa00000001', 'ji00000003', '佐藤三郎', 'satou@saburou.com', '$2y$10$0545288pe8P/P/kgy2mZM.8QfnR0Gn.SApnIfRJhefzwa8QGX79/2', '2021-09-06 10:24:22', '13', 'ji00000003', 0, 0, '2021-09-06 10:24:22', NULL, NULL, '2021-09-06 01:24:22', '2021-09-06 01:24:22'],
            ['aa00000001', 'ji00000004', '佐々木花子', 'sasaki@hanako.com', '$2y$10$TZ/smM.VJdhY6d9XzYnHTOBNtC4sPUndl/znyH9N97vBXHUPwX42S', '2021-09-06 10:25:37', '18', 'ji00000004', 0, 0, '2021-09-06 10:24:22', '2021-09-06 10:25:37', NULL, '2021-09-06 01:25:37', '2021-09-06 01:25:37'],
            ['aa00000001', 'ji00000005', '佐々木京子', 'sasaki@kyouko.com', '$2y$10$6cZOWiFb6h.1P/.akvikMeG6QoPXvO0DZQrXqFovA5ScdkUV1ppGm', '2021-09-06 10:26:08', '14', 'ji00000005', 1, 1, '2021-09-06 10:24:22', NULL, NULL, '2021-09-06 01:26:08', '2021-09-06 01:26:08'],
            ['aa00000001', 'ji00000006', '上田四郎', 'ueda@sirou.com', '$2y$10$MgSpP5wuWKwSpdMlcfr4GuhZ0hJ.C4ZkKn/d8IdnjzYVdFDeofLKy', '2021-09-06 10:27:49', '11', 'ji00000006', 0, 0, '2021-09-06 10:24:22', NULL, NULL, '2021-09-06 01:27:49', '2021-09-06 01:27:49'],
            ['aa00000001', 'ji00000007', '下山五郎', 'simoyama@gorou.com', '$2y$10$Sr9RRVQD1VVGs6Robx1yjOqlBIpaLc1bPWas4t7e/s8OZNyBG65nS', '2021-09-06 10:29:36', '13', 'ji00000007', 1, 1, '2021-09-06 10:29:36', NULL, NULL, '2021-09-06 01:29:36', '2021-09-06 01:29:36'],
            ['aa00000001', 'ji00000008', '浅田桜子', 'ninzin', '$2y$10$chTpoLp.IdkgTk2EFPcLZ.0oAkTIA84QKmqmbZWG0USB15IM.XfDG', '2021-09-06 10:30:12', '14', 'ji00000008', 1, 0, '2021-09-06 00:00:00', NULL, NULL, '2021-09-06 01:30:12', '2021-12-09 05:39:22'],
            ['aa00000001', 'ji00000009', '山田恭子', 'yamada@kyouko.com', '$2y$10$I5YGcb564hVxuxtgCfU8X.e8vlqLDm17eR4NXorktbyAp5y2yYNJG', '2021-09-06 10:31:34', '13', 'ji00000009', 1, 1, '2021-09-06 10:31:34', NULL, NULL, '2021-09-06 01:31:34', '2021-09-06 01:31:34'],
            ['aa00000001', 'ji00000010', '高橋花子', 'takahasi@hanako.com', '$2y$10$AjVYz1C75May3.ykuUBI5OuJueL2dl967AYLlIG4biOZt5K1hoLQW', '2021-09-06 10:32:25', '12', 'ji00000010', 0, 0, '2021-09-06 10:31:34', NULL, NULL, '2021-09-06 01:32:25', '2021-09-06 01:32:25']
        ];
        foreach($dcji_insert_data_list as $insert_data){
            DB::insert('insert into dcji01 ('. $dcji_columns .') VALUE ('. $dcji_holder .')', $insert_data);
        }

    }
}