<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDcsb01Table extends Migration
{
    /**
     * Run the migrations.
     * 作業場所のテーブル
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcsb01', function (Blueprint $table) {
            $table->primary(['client_id', 'space_id']);
            $table->string('client_id', '10')
                ->comment('顧客ID');
            $table->string('space_id', '10')
                ->comment('作業場所ID');
            $table->string('space_name', '32')
                ->comment('作業場所名称');
            $table->string('management_personnel_id', '10')
                ->comment('管理者人員番号');
            $table->unsignedInteger('post_code')->index()
                ->comment('郵便番号');
            $table->string('prefectural_office_location')
                ->comment('都道府県');
            $table->string('address')
                ->comment('市区町村');
            $table->string('URL', '256')->nullable()
                ->comment('地図URL');
            $table->string('remarks', '512')->nullable()
                ->comment('備考');
            $table->timestamp('created_at')->useCurrent()
                ->comment('登録日');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))
                ->comment('修正日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dcsb01');
    }
}
