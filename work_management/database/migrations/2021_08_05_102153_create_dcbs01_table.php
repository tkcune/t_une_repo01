<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcbs01Table extends Migration
{
    /**
     * 部署テーブルマイグレーション
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcbs01', function (Blueprint $table) {
            $table->primary(['client_id', 'department_id']);
            $table->string('client_id','10');
            $table->string('department_id','10');
            $table->string('responsible_person_id','10');
            $table->string('name','255');
            $table->string('status','2');
            $table->string('management_personnel_id','10');
            $table->datetime('operation_start_date')->nullable();
            $table->datetime('operation_end_date')->nullable();
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
        Schema::dropIfExists('dcbs01');
    }
}
