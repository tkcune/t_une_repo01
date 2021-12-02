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
        DB::insert('insert into dclg01 (client_id, log_id, created_at, updated_at, type, user, function, program_pass, log)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['kk00000000', 1, '2021-12-01 03:01:36', '2021-12-01 03:01:36', 'si', 'log@log.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']);
        
        DB::insert('insert into dclg01 (client_id, log_id, created_at, updated_at, type, user, function, program_pass, log)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['kk00000000', 2, '2021-12-01 03:03:59', '2021-12-01 03:03:59', 'si', 'log@log.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']);
        
        DB::insert('insert into dclg01 (client_id, log_id, created_at, updated_at, type, user, function, program_pass, log)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['kk00000000', 3, '2021-12-01 03:04:03', '2021-12-01 03:04:03', 'si', 'log@log.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']);
        
        DB::insert('insert into dclg01 (client_id, log_id, created_at, updated_at, type, user, function, program_pass, log)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['kk00000000', 4, '2021-12-01 03:04:06', '2021-12-01 03:04:06', 'si', 'log@log.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']);
        
        DB::insert('insert into dclg01 (client_id, log_id, created_at, updated_at, type, user, function, program_pass, log)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['kk00000000', 5, '2021-12-01 03:09:28', '2021-12-01 03:09:28', 'si', 'log@log.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']);

        DB::insert('insert into dclg01 (client_id, log_id, created_at, updated_at, type, user, function, program_pass, log)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['kk00000000', 6, '2021-12-01 03:09:32', '2021-12-01 03:09:32', 'si', 'log@log.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']);

        DB::insert('insert into dclg01 (client_id, log_id, created_at, updated_at, type, user, function, program_pass, log)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['kk00000000', 7, '2021-12-01 03:09:35', '2021-12-01 03:09:35', 'si', 'log@log.com', 'cm', 'C:\\work\\sagyokanri\\work_management\\app\\Http\\Middleware\\StartWorkManegement.php:22', '処理開始']);
    }
}
