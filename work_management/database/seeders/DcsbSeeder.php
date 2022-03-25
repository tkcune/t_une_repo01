<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DcsbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 作業場所のシーダファイル
     *
     * @return void
     */
    public function run()
    {
        //@var string dcsb01のカラム列
        $dcsb_columns = 'client_id,space_id, space_name, management_personnel_id, post_code, prefectural_office_location, address, URL, remarks, created_at, updated_at';
        //@var string カラム列のホルダー
        $dcsb_holder = '?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
        //@var array dcsb01の挿入データ
        $dcsb_insert_data_list = [
            ['aa00000001', 'sb00000001', '営業部', 'ji00000001', '6078142', '京都府京都市', '山科区東野中井ノ上町', NULL, NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
            ['aa00000001', 'sb00000002', '経理部', 'ji00000002', '6078173', '京都府京都市', '山科区北花山六反田町', NULL, NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
            ['aa00000001', 'sb00000003', '鉄鋼部', 'ji00000001', '6078173', '京都府京都市', '山科区北花山六反田町', NULL, NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
            ['aa00000001', 'sb00000004', '社長室', 'ji00000001', '6078142', '京都府京都市', '山科区東野中井ノ上町', NULL, NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
        ];
        foreach ($dcsb_insert_data_list as $insert_data) {
            DB::insert('insert into dcsb01 (' . $dcsb_columns . ') VALUE (' . $dcsb_holder . ')', $insert_data);
        }
    }
}
