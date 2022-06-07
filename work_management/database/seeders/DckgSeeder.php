<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DckgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //@var string dcji01のカラム列
        $dckg_columns = 'client_id, authority_id, authority_path_id, own_authority_id, reference_authority, register_authority, update_authority, delete_authority, authority_personnel_id';
        //@var string カラム列のホルダー
        $dckg_holder = '?, ?, ?, ?, ?, ?, ?, ?, ?';
        //@var array dcbs01の挿入データ
        $dckg_insert_data_list = [
            ['aa00000001', 'kg00000001', 'bs00000001', 'ji00000001', '1', '1', '1', '1', 'ji00000001'],
            ['aa00000001', 'kg00000002', 'bs00000002', 'ji00000003', '1', '1', '0', '1', 'ji00000001'],
            ['aa00000001', 'kg00000003', 'bs00000002', 'ji00000008', '1', '1', '1', '1', 'ji00000001'],
            ['aa00000001', 'kg00000004', 'bs00000001', 'ji00000004', '1', '1', '1', '1', 'ji00000001'],
            ['aa00000001', 'kg00000005', 'bs00000006', 'ji00000003', '1', '1', '1', '1', 'ji00000001'],
            ['aa00000001', 'kg00000005', 'ji00000005', 'ji00000003', '1', '1', '1', '0', 'ji00000001']
        ];
        foreach($dckg_insert_data_list as $insert_data){
            DB::insert('insert into dckg01 ('. $dckg_columns .') VALUE ('. $dckg_holder .')', $insert_data);
        }

    }
}
