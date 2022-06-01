<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcsv01Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcsv01', function (Blueprint $table) {
            $table->primary(['client_id', 'personnel_id','board_id','stamp_id']);
            $table->string('client_id','10');
            $table->string('personnel_id','10');
            $table->string('board_id','10');
            $table->integer('stamp_id','2');
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
        Schema::dropIfExists('dcsv01');
    }
}
