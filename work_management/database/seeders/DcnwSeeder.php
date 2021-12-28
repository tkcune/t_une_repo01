<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DcnwSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert('insert into dcnw01 (client_id, name, email, password, recieving_server, recieving_server_way, recieving_port_number, sending_server, sending_server_way, sending_port_number)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            ['aa00000001', 'sagyotest@b-forme.net', 'sagyotest@b-forme.net', 'sagyopass', 'imap4.muumuu-mail.com', '1', '993', 'smtp.muumuu-mail.com', '1', '587']);
    }
}
