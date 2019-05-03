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
            $table->integer('user_id')->unsigned();
            $table->text('address')->nullable();
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('contact');
            $table->string('photo')->nullable();
            $table->integer('hourly_rate')->default('0')->nullable();
            $table->integer('annual_salary')->default('0')->nullable();
            $table->text('description')->nullable();
            $table->date('employment_start_date')->nullable();
            $table->longText('documents')->nullable();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

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
