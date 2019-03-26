<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('photo');
            $table->text('address');
            $table->string('gender')->nullable();
            $table->string('contact')->nullable();
            $table->integer('hourly_rate')->default('0');
            $table->integer('annual_salary')->default('0');
            $table->text('description');
            $table->date('date_of_birth');
            $table->date('employment_start_date');
            $table->text('documents')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
