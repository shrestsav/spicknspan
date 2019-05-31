<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('added_by')->nullable();

            //useless field but still in use in some cases
            $table->string('user_type')->default('employee')->nullable(); 
            //useless field
            $table->integer('mark_default')->default('0')->nullable(); 
            //useless field, simply use permission
            $table->integer('inspection')->default('0')->nullable(); 

            $table->longText('client_ids')->nullable();
            $table->string('timezone')->default('Australia/Sydney');

            // Socialite Fields
            $table->string('g_id')->nullable();
            $table->string('f_id')->nullable();
            $table->string('avatar')->nullable();
            $table->string('access_token')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
