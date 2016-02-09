<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('org_id')->default(0);
            $table->integer('role_id')->default(0);
			$table->char('is_active', 1)->default('1');
			$table->char('is_deleted', 1)->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->timestamp('deleted_at')->nullable();
			
			$table->string('api_token')->nullable();
            $table->string('name', 60)->unique();
			$table->text('description')->nullable();
            $table->string('email')->unique();
            $table->string('password', 80);
            $table->string('salt', 40)->nullable();
            $table->rememberToken();
            $table->bigInteger('last_login')->nullable();
 			$table->char('is_online', 1)->default('0');
            $table->integer('supervisor_id')->nullable();
            $table->integer('bpartner_id')->nullable();
 			$table->char('is_fullbpaccess', 1)->default('1');
 			$table->char('is_expired', 1)->default('0');
            $table->string('security_question', 120)->nullable();
            $table->string('security_answer', 120)->nullable();
            $table->string('ip_address', 16)->nullable();
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('a_user');
    }
}
