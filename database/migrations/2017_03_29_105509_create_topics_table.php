<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',10)->comment('话题名称')->unique();
            $table->string('description')->nullable()->comment('话题描述'); //可以为空
            $table->unsignedInteger('parent_id')->comment('父话题id');
            $table->string('logo')->comment('话题logo'); //50*50
            $table->unsignedInteger('followers_count')->default(0)->comment('该话题被关注的次数');
            $table->unsignedInteger('questions_count')->default(0)->comment('该话题中的问题数目');
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
        Schema::dropIfExists('topics');
    }
}
