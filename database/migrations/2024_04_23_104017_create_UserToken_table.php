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
        Schema::create('UserToken', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('token');
            $table->char('userID');
            $table->integer('isMFA')->default(0);
            $table->integer('isLogOut')->default(0);
            $table->char('logOutTime', 10)->nullable();
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('UserToken');
    }
};
