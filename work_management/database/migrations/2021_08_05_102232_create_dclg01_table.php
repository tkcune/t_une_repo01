<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDclg01Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dclg01', function (Blueprint $table) {
            $table->primary(['client_id', 'log_id']);
            $table->string('client_id','10');
            $table->bigInteger('log_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('type','2');
            $table->string('user','255');
            $table->string('function');
            $table->string('program_pass');
            $table->string('log');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dclg01');
    }
}
