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
            $table->unsignedInteger('likes_count')->default(0)->comment('赞的次数');
            $table->unsignedInteger('comments_count')->default(0)->comment('评论次数');
            $table->unsignedTinyInteger('is_comment')->default(0)->comment('能否评论');
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
        Schema::dropIfExists('answers');
    }
}
