<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAFormAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_form_access', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('org_id')->default(0);
            $table->integer('role_id');
            $table->integer('form_id');
			$table->char('is_active', 1)->default('1');
			$table->char('is_deleted', 1)->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->timestamp('deleted_at')->nullable();

			$table->char('execute', 1)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_form_access');
    }
}
