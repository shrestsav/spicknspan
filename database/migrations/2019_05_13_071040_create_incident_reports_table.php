<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->longText('type')->nullable();
            $table->string('person_involved')->nullable();
            $table->string('occupation')->nullable();
            $table->integer('employer_id')->unsigned()->nullable();
            $table->string('contact')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('date')->nullable();
            $table->longText('medical_treatment')->nullable();
            $table->smallInteger('cease_work')->nullable()->comment('0: No, 1:Yes');
            $table->longText('attended_authorities')->nullable();
            $table->text('desc_what')->nullable();
            $table->text('desc_how')->nullable();
            $table->text('desc_why')->nullable();
            $table->text('desc_immediate_actions')->nullable();
            $table->text('desc_relevant_controls')->nullable();
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
        Schema::dropIfExists('incident_reports');
    }
}
