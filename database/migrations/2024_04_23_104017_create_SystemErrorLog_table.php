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
        Schema::create('SystemErrorLog', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('method', 4);
            $table->string('ip', 30);
            $table->string('api');
            $table->string('request');
            $table->string('errorMessage');
            $table->string('errorLine', 10);
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
        Schema::dropIfExists('SystemErrorLog');
    }
};
