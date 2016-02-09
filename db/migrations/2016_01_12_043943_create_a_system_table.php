<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateASystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_system', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('org_id');
			$table->char('is_active', 1)->default('1');
			$table->char('is_deleted', 1)->default('0');
            $table->integer('create_by')->nullable();
            $table->integer('modify_by')->nullable();
            $table->integer('deleted_by')->nullable();
			$table->timestamp('create_at')->nullable();
			$table->timestamp('modify_at')->nullable();
			$table->timestamp('deleted_at')->nullable();

			$table->string('api_token')->unique();
            $table->string('name', 60);
			$table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_system');
    }
}
