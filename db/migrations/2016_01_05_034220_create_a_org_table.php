<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAOrgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_org', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
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
            $table->integer('supervisor_id')->nullable();
            $table->integer('parent_org_id')->nullable();
            $table->integer('orgtype_id')->nullable();
            $table->integer('transit_warehouse_id')->nullable();
            $table->string('address_map', 120)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('phone2', 40)->nullable();
            $table->string('fax', 40)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('website', 120)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_org');
    }
}
