<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DcbsSeeder::class,
            DccmksSeeder::class,
            DccmtaSeeder::class,
            DcjiSeeder::class,
            DclgSeeder::class,
            DcnwSeeder::class,
            DckbSeeder::class,
            DcsbSeeder::class,
            DcspSeeder::class,
            DcsvSeeder::class
        ]);
    }
}
