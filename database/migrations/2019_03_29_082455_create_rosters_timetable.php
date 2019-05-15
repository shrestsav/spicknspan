<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRostersTimetable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster_timetables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('roster_id')->unsigned();
            $table->date('full_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->smallInteger('status')->nullable()->comment('1:Approved, 2:Declined');
            $table->integer('approved_by')->nullable()->unsigned();
            $table->text('remarks')->nullable();
            $table->foreign('roster_id')->references('id')->on('rosters')
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
        Schema::dropIfExists('roster_timetables');
    }
}
