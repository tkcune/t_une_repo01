<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DcbsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //@var string dcbs01のカラム列
        $dcbs_columns = 'client_id, department_id, responsible_person_id, name, status, management_personnel_id, operation_start_date, operation_end_date, remarks, created_at, updated_at';
        //@var string カラム列のホルダー
        $dcbs_holder = '?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
        //@var array dcbs01の挿入データ
        $dcbs_insert_data_list = [
            ['aa00000001', 'bs00000001', 'ji00000001', '部署A', '10', 'ji00000001', '2021-09-06 10:21:02', NULL, NULL, '2021-09-06 01:20:38', '2021-09-06 01:20:38'],
            ['aa00000001', 'bs00000002', 'ji00000001', '部署B', '12', 'ji00000001', '2021-09-06 10:21:02', NULL, NULL, '2021-09-06 01:20:53', '2021-09-06 01:20:53'],
            ['aa00000001', 'bs00000003', 'ji00000001', '部署C', '13', 'ji00000001', '2021-09-06 10:21:02', NULL, NULL, '2021-09-06 01:21:02', '2021-09-06 01:21:02'],
            ['aa00000001', 'bs00000004', 'ji00000001', '部署D', '14', 'ji00000001', '2021-09-06 10:21:02', NULL, NULL, '2021-09-06 01:21:12', '2021-09-06 01:21:12'],
            ['aa00000001', 'bs00000005', 'ji00000001', '部署E', '18', 'ji00000001', '2021-09-06 10:21:02', '2021-09-06 10:21:21', NULL, '2021-09-06 01:21:21', '2021-09-06 01:21:21'],
            ['aa00000001', 'bs00000006', 'ji00000001', '部署F', '11', 'ji00000001', '2021-09-06 10:21:02', NULL, NULL, '2021-09-06 01:21:33', '2021-09-06 01:21:33'],
            ['aa00000001', 'bs00000007', 'ji00000001', '部署G', '13', 'ji00000001', '2021-09-06 10:28:10', NULL, NULL, '2021-09-06 01:28:10', '2021-09-06 01:28:10'],
            ['aa00000001', 'bs00000008', 'ji00000001', '部署H', '13', 'ji00000001', '2021-09-06 10:28:19', NULL, NULL, '2021-09-06 01:28:19', '2021-09-06 01:28:19'],
            ['aa00000001', 'bs00000009', 'ji00000001', '部署I', '14', 'ji00000001', '2021-09-07 10:28:19', NULL, NULL, '2021-09-06 01:28:26', '2021-09-06 01:28:26']
        ];
        foreach($dcbs_insert_data_list as $insert_data){
            DB::insert('insert into dcbs01 ('. $dcbs_columns .') VALUE ('. $dcbs_holder .')', $insert_data);
        }
    }
}