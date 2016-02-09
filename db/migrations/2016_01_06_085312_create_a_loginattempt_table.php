<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateALoginattemptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_loginattempt', function (Blueprint $table) {
            $table->increments('id');
            $table->string('remote_addr', 60)->nullable();
            $table->string('remote_host', 120)->nullable();
            $table->string('login', 60)->nullable();
            $table->string('ip_address', 16)->nullable();
            $table->bigInteger('time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_loginattempt');
    }
}
