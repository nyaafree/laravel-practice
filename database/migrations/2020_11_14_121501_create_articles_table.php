<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
              //==========ここから追加==========
              $table->string('title');
              $table->text('body');
              $table->bigInteger('user_id');
              //   外部キー制約　https://www.techpit.jp/courses/11/curriculums/12/sections/107/parts/388
              //   articlesテーブルのuser_idカラムは、usersテーブルのidカラムを参照すること
              $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('articles');
    }
}
