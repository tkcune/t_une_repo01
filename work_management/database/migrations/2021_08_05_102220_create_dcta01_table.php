<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcta01Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dccmta', function (Blueprint $table) {
            $table->primary(['client_id', 'projection_id']);
            $table->string('client_id','10');
            $table->string('projection_id','10');
            $table->string('projection_source_id','10');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dcta01');
    }
}
