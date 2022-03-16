<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DckbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dckb01')->insert([
            'client_id' => 'aa00000001',
            'board_id' => 'kb00000001',
            'name' => 'sample',
            'status' => '11',
            'management_personnel_id' =>'ji00000001',
            'remarks' =>'sample',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dckb01')->insert([
            'client_id' => 'aa00000001',
            'board_id' => 'kb00000002',
            'name' => 'sample2',
            'status' => '12',
            'management_personnel_id' =>'ji00000001',
            'remarks' =>'sample2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dckb01')->insert([
            'client_id' => 'aa00000001',
            'board_id' => 'kb00000003',
            'name' => 'sample3',
            'status' => '12',
            'management_personnel_id' =>'ji00000001',
            'remarks' =>'sample3',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dckb01')->insert([
            'client_id' => 'aa00000001',
            'board_id' => 'kb00000004',
            'name' => 'sample4',
            'status' => '12',
            'management_personnel_id' =>'ji00000001',
            'remarks' =>'sample4',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('dckb01')->insert([
            'client_id' => 'aa00000001',
            'board_id' => 'kb00000005',
            'name' => 'sample5',
            'status' => '12',
            'management_personnel_id' =>'ji00000001',
            'remarks' =>'sample5',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
