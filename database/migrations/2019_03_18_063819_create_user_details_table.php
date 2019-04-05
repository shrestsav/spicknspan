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
            $table->text('address');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('contact');
            $table->string('photo')->nullable();
            $table->integer('hourly_rate')->default('0');
            $table->integer('annual_salary')->default('0');
            $table->text('description')->nullable();
            $table->date('employment_start_date')->nullable();
            $table->text('documents')->nullable();
            $table->string('timezone')->default('Australia/Sydney');
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
