<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcji01Table extends Migration
{
    /**
     * 人員テーブルマイグレーション
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('dcji01', function (Blueprint $table) {
            $table->primary(['client_id', 'personnel_id']);
            $table->string('client_id','10');
            $table->string('personnel_id','10');
            $table->string('name','255');
            $table->string('email','255');
            $table->string('password','255');
            $table->datetime('password_update_day');
            $table->string('status','2');
            $table->string('management_personnel_id','10');
            $table->boolean('login_authority');
            $table->boolean('system_management');
            $table->datetime('operation_start_date')->nullable();
            $table->datetime('operation_end_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dcji01');
    }
}
