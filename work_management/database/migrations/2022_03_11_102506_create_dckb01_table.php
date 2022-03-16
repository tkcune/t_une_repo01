<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDckb01Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dckb01', function (Blueprint $table) {
            $table->primary(['client_id', 'board_id']);
            $table->string('client_id','10');
            $table->string('board_id','10');
            $table->string('name','255');
            $table->string('status','2');
            $table->string('management_personnel_id','10');
            $table->string('remarks', '1024')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dckb01');
    }
}
