<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',20)->comment('问题名称');
            $table->text('content')->nullable()->comment('问题说明');
            $table->unsignedInteger('user_id')->index()->comment('问题创建者');
//            $table->unsignedInteger('answers_count')->default(0)->comment('该问题的答案数量'); //暂时采用直接计算
            $table->unsignedInteger('followers_count')->default(0)->comment('该问题的关注者数量');
            $table->unsignedInteger('browses_count')->default(1)->comment('该问题被浏览的次数');
            $table->unsignedTinyInteger('is_show')->default(1)->index()->comment('是否显示改问题');
            $table->unsignedTinyInteger('is_show_user')->default(1)->index()->comment('是否公开姓名');
            $table->timestamps();
        });

        //question_topic
        Schema::create('question_topic', function (Blueprint $table) {
            $table->unsignedInteger('question_id')->index();
            $table->unsignedInteger('topic_id')->index();
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
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_topic');
    }
}
