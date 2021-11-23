<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DccmtaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert('insert into dccmta (client_id, projection_id, projection_source_id, created_at, updated_at)
            VALUE (?, ?, ?, ?, ?)',
            ['aa00000001', 'ta00000001', 'bs00000002', NULL, NULL]);

        DB::insert('insert into dccmta (client_id, projection_id, projection_source_id, created_at, updated_at)
            VALUE (?, ?, ?, ?, ?)',
            ['aa00000001', 'ta00000002', 'bs00000004', NULL, NULL]);

        DB::insert('insert into dccmta (client_id, projection_id, projection_source_id, created_at, updated_at)
            VALUE (?, ?, ?, ?, ?)',
            ['aa00000001', 'ta00000003', 'bs00000004', NULL, NULL]);

        DB::insert('insert into dccmta (client_id, projection_id, projection_source_id, created_at, updated_at)
            VALUE (?, ?, ?, ?, ?)',
            ['aa00000001', 'ta00000004', 'bs00000006', NULL, NULL]);

        DB::insert('insert into dccmta (client_id, projection_id, projection_source_id, created_at, updated_at)
            VALUE (?, ?, ?, ?, ?)',
            ['aa00000001', 'ta00000005', 'bs00000006', NULL, NULL]);
    }
}
