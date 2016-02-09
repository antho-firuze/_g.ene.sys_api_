<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_process', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('org_id')->default(0);
			$table->char('is_active', 1)->default('1');
			$table->char('is_deleted', 1)->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->timestamp('deleted_at')->nullable();

            $table->string('name', 60);
			$table->text('description')->nullable();
			$table->text('help')->nullable();
            $table->string('path', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_process');
    }
}
