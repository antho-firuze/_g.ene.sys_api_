<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateARoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_role', function (Blueprint $table) {
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
			
			$table->string('name', 100)->nullable();
			$table->text('description')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('supervisor_id')->nullable();
            $table->decimal('amt_approval', 18, 2)->default(0);
			$table->char('is_canexport', 1)->default('1');
			$table->char('is_canreport', 1)->default('1');
			$table->char('is_canapproveowndoc', 1)->default('1');
			$table->char('is_accessallorgs', 1)->default('0');
			$table->char('is_useuserorgaccess', 1)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_role');
    }
}
