<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Users', function (Blueprint $table) {
            $table->integer('id', true)->comment('流水號');
            $table->char('userID', 32)->comment('用戶ID');
            $table->char('name', 40);
            $table->char('account', 32)->comment('帳號');
            $table->char('password', 32)->comment('密碼');
            $table->char('email', 40)->comment('信箱');
            $table->integer('toggle')->default(1)->comment('啟用狀態');
            $table->integer('errorCount')->default(0)->comment('登入錯誤次數');
            $table->integer('isDeleted')->default(0)->comment('是否刪除帳號');
            $table->char('googleAuthCode')->comment('GoogelAuthCode');
            $table->char('googleAuthQrcodeUrl')->comment('GoogelQRCodeUrl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Users');
    }
};
