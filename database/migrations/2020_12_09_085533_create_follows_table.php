<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->bigIncrements('id');
            //==========ここから追加==========
            $table->bigInteger('follower_id');
            $table->foreign('follower_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->bigInteger('followee_id');
            $table->foreign('followee_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            //==========ここまで追加==========
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
        Schema::dropIfExists('follows');
    }
}