<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDcnwTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcnw01', function (Blueprint $table) {
            $table->primary('client_id');
            $table->string('client_id', '10');
            $table->string('name', '256');
            $table->string('email', '256');
            $table->string('password', '256');
            $table->string('recieving_server', '256');
            $table->string('recieving_server_way', '1');
            $table->integer('recieving_port_number');
            $table->string('sending_server', '256');
            $table->string('sending_server_way', '1');
            $table->integer('sending_port_number');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('update_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dcnw01');
    }
}
