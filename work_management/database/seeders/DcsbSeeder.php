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
            ['aa00000001', 'sb00000001', 'A工場', 'ji00000001', '4200813', '静岡県静岡市', '葵区長沼500-12', 'https://www.google.com/maps?ll=34.989119,138.411453&z=16&t=m&hl=ja&gl=JP&mapclient=embed&q=%E3%80%92420-0813+%E9%9D%99%E5%B2%A1%E7%9C%8C%E9%9D%99%E5%B2%A1%E5%B8%82%E8%91%B5%E5%8C%BA%E9%95%B7%E6%B2%BC%EF%BC%95%EF%BC%90%EF%BC%90%E2%88%92%EF%BC%91%EF%BC%92', NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
            ['aa00000001', 'sb00000002', 'B部署', 'ji00000002', '2310023', '神奈川県横浜市', '中区山下町279-25山下ふ頭内', NULL, NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
            ['aa00000001', 'sb00000003', 'C傘下', 'ji00000001', '2750016', '千葉県習志野市', '津田沼1', NULL, NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
            ['aa00000001', 'sb00000004', 'D下請', 'ji00000001', '9030815', '沖縄県那覇市', '首里金城町1-2', 'https://x.gd/KE3t1', NULL, '2022-03-03 01:22:19', '2022-03-03 01:22:19'],
        ];
        foreach ($dcsb_insert_data_list as $insert_data) {
            DB::insert('insert into dcsb01 (' . $dcsb_columns . ') VALUE (' . $dcsb_holder . ')', $insert_data);
        }
    }
}
