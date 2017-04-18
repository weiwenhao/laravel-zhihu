<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content')->comment('答案内容');
            $table->unsignedInteger('user_id')->index()->comment('用户id');
            $table->unsignedInteger('question_id')->index()->comment('问题id');
            $table->unsignedInteger('likes_count')->default(0)->comment('赞的次数');    //赞属于答案,只能存储在答案
            $table->unsignedInteger('comments_count')->default(0)->comment('评论次数'); //评论次数本质上并不属于答案,可以通过对该问题的答案计数得到
//            $table->unsignedTinyInteger('is_comment')->default(1)->comment('能否评论');
            $table->unsignedTinyInteger('is_deleted')->default(0)->index()->comment('是否删除');  //类似这种查询中一定会用到的最好都加上
            $table->timestamps();
            $table->unique(['user_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
