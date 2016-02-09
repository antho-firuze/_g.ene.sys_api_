<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateASessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_session', function (Blueprint $table) {
            $table->increments('id');
            $table->string('remote_addr', 60);
            $table->string('remote_host', 120);
            $table->string('user_agent', 120);
            $table->text('user_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_session');
    }
}
