<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content', 300)->comment('评论内容');
            $table->unsignedInteger('answer_id')->index()->comment('所属回答id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('obj_comment_id')->nullable()->comment('回复的目标用户');
            $table->string('obj_username', 10)->nullable()->comment('回复的目标用户');
            $table->unsignedInteger('likes_count')->default(0)->comment('被点赞的次数'); //这里应该独立一个点赞表出来
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
        Schema::dropIfExists('comments');
    }
}
