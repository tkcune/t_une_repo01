<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDckg01Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dckg01', function (Blueprint $table) {
            $table->primary(['client_id', 'authority_id', 'authority_path_id']);
            $table->string('client_id','10')->comment('クライアントのid');
            $table->string('authority_id','10')->comment('権限のid');
            $table->string('authority_path_id','10')->comment('権限場所のid');
            $table->string('own_authority_id','10')->comment('権限所有者');
            $table->string('reference_authority','1')->comment('参照権限');
            $table->string('register_authority','1')->comment('登録権限');
            $table->string('update_authority','1')->comment('更新権限');
            $table->string('delete_authority','1')->comment('削除権限');
            $table->string('authority_personnel_id','10')->comment('権限付与者');
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
        Schema::dropIfExists('dckg01');
    }
}
