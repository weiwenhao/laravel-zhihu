<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 10);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('logo', 255)->default('/img/user_logo.jpg')->comment('头像 50 x 50');
            $table->string('big_log', 255)->default('/img/user_big_logo.jpg')->comment('头像 200 x 200');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
