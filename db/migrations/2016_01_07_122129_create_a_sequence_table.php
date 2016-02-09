<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateASequenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_sequence', function (Blueprint $table) {
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
            $table->integer('start_no')->default(1);
            $table->integer('increment_no')->default(1);
            $table->integer('startnewyear')->default(1);
            $table->integer('startnewmonth')->default(0);
            $table->smallInteger('digit')->default(5);
            $table->string('prefix', 60);
            $table->string('suffix', 60);
            $table->string('sign_title1', 120);
            $table->string('sign_title2', 120);
            $table->string('sign_title3', 120);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_sequence');
    }
}
