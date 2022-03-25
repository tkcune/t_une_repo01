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
     * @param $space_id 作業場所ID
     * @param $space_name　作業場所名称
     * @param $management_personnel_id　管理者人員番号
     * @param $post_code 郵便番号
     * @param $prefectural_office_location 都道府県
     * @param $address 市区町村
     * @param $URL アドレス
     * @param $remarks 備考
     * @param $created_at 登録日
     * @param $updated_at 修正日
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcsb01', function (Blueprint $table) {
            $table->primary(['client_id', 'space_id']);            $table->string('client_id', '10');
            $table->string('space_id', '10');
            $table->string('space_name', '32');
            $table->string('management_personnel_id', '10');
            $table->unsignedInteger('post_code')->index();
            $table->string('prefectural_office_location');
            $table->string('address');
            $table->string('URL', '256')->nullable();
            $table->string('remarks', '512')->nullable();
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
        Schema::dropIfExists('dcsb01');
    }
}
