<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert(
            'insert into users (name,email,password,password_update_day,created_at,updated_at)
            VALUE (?, ?, ?, ?, ?, ?)',
            ['abcd', 'abcd@sample.jp', '$2y$10$sbeMXkbMYD747RIk6wl.geyb4VdQMGEEd4jfgClCAlX8unnWTaYn.', '2022-09-13','2022-06-14 23:59:59','2022-06-14 23:59:59']
        );

    }
}
