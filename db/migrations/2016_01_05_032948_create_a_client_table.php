<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_client', function (Blueprint $table) {
            $table->increments('id');
			$table->char('is_active', 1)->default('1');
			$table->char('is_deleted', 1)->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->timestamp('deleted_at')->nullable();

            $table->string('code', 40);
            $table->string('name', 60);
			$table->text('description')->nullable();
			$table->char('mmpolicy', 1)->default('F');
			$table->string('smtp_host', 60)->nullable();
            $table->integer('smtp_port')->nullable();
			$table->char('is_securesmtp', 1)->default('0');
            $table->string('logoapp_path', 255)->nullable();
            $table->string('logoweb_path', 255)->nullable();
            $table->string('logoreport_path', 255)->nullable();
			$table->char('is_confirmondocclose', 1)->default('1');
			$table->char('is_confirmondocvoid', 1)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_client');
    }
}
