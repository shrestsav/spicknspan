<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->integer('employee_id');
            $table->date('full_date')->nullable(); // Can be removed later, redundant field
            $table->dateTime('check_in');
            $table->text('check_in_location');            
            $table->text('check_in_image');            
            $table->dateTime('check_out')->nullable();
            $table->text('check_out_location')->nullable();
            $table->text('check_out_image')->nullable(); 
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('attendances');
    }
}
